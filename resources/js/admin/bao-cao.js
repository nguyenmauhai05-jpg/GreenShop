(function () {
    'use strict';

    const canvas = document.getElementById('revenueChart');

    if (!canvas) {
        return;
    }

    const tooltip = document.getElementById('revenueChartTooltip');
    const rawData = Array.isArray(window.reportChartData)
        ? window.reportChartData
        : [];

    const groupBy = String(window.reportGroupBy || 'ngay');

    const data = rawData
        .map(function (item) {
            return {
                label: String(item.nhom ?? ''),
                value: Number(item.doanh_thu ?? 0),
            };
        })
        .filter(function (item) {
            return item.label !== '' && Number.isFinite(item.value);
        });

    if (!data.length) {
        return;
    }

    const ctx = canvas.getContext('2d');
    let points = [];
    let animationFrame = null;

    function money(value) {
        return new Intl.NumberFormat('vi-VN').format(Math.round(value)) + '₫';
    }

    function shortMoney(value) {
        const amount = Number(value) || 0;

        if (amount >= 1000000000) {
            return (amount / 1000000000).toFixed(amount % 1000000000 === 0 ? 0 : 1) + ' tỷ';
        }

        if (amount >= 1000000) {
            return (amount / 1000000).toFixed(amount % 1000000 === 0 ? 0 : 1) + ' tr';
        }

        if (amount >= 1000) {
            return (amount / 1000).toFixed(amount % 1000 === 0 ? 0 : 1) + 'k';
        }

        return String(Math.round(amount));
    }

    function labelText(label) {
        if (groupBy === 'ngay' && /^\d{4}-\d{2}-\d{2}$/.test(label)) {
            const parts = label.split('-');
            return parts[2] + '/' + parts[1];
        }

        if (groupBy === 'thang' && /^\d{4}-\d{2}$/.test(label)) {
            const parts = label.split('-');
            return parts[1] + '/' + parts[0];
        }

        return label;
    }

    function roundMax(value) {
        if (value <= 0) {
            return 1;
        }

        const power = Math.pow(10, Math.floor(Math.log10(value)));
        const normalized = value / power;

        let nice = 1;

        if (normalized <= 1) nice = 1;
        else if (normalized <= 2) nice = 2;
        else if (normalized <= 5) nice = 5;
        else nice = 10;

        return nice * power;
    }

    function resizeCanvas() {
        const box = canvas.getBoundingClientRect();
        const ratio = Math.max(1, window.devicePixelRatio || 1);

        canvas.width = Math.round(box.width * ratio);
        canvas.height = Math.round(box.height * ratio);

        ctx.setTransform(ratio, 0, 0, ratio, 0, 0);

        return {
            width: box.width,
            height: box.height,
        };
    }

    function draw() {
        const size = resizeCanvas();
        const width = size.width;
        const height = size.height;

        ctx.clearRect(0, 0, width, height);

        const padding = {
            top: 24,
            right: 24,
            bottom: 42,
            left: 62,
        };

        const chartWidth = Math.max(1, width - padding.left - padding.right);
        const chartHeight = Math.max(1, height - padding.top - padding.bottom);

        const rawMax = Math.max.apply(
            null,
            data.map(function (item) {
                return item.value;
            }).concat([1])
        );

        const maxValue = roundMax(rawMax * 1.12);
        const gridLines = 5;

        ctx.font = '10px "Segoe UI", Tahoma, Geneva, Verdana, sans-serif';
        ctx.textBaseline = 'middle';

        // Grid + Y labels
        for (let i = 0; i <= gridLines; i += 1) {
            const ratio = i / gridLines;
            const y = padding.top + chartHeight - chartHeight * ratio;
            const value = maxValue * ratio;

            ctx.beginPath();
            ctx.moveTo(padding.left, y);
            ctx.lineTo(padding.left + chartWidth, y);
            ctx.strokeStyle = '#edf1ed';
            ctx.lineWidth = 1;
            ctx.stroke();

            ctx.fillStyle = '#8a968e';
            ctx.textAlign = 'right';
            ctx.fillText(shortMoney(value), padding.left - 10, y);
        }

        // X-axis
        ctx.beginPath();
        ctx.moveTo(padding.left, padding.top + chartHeight);
        ctx.lineTo(padding.left + chartWidth, padding.top + chartHeight);
        ctx.strokeStyle = '#dfe6e0';
        ctx.lineWidth = 1;
        ctx.stroke();

        const slotWidth = chartWidth / Math.max(data.length, 1);

        points = data.map(function (item, index) {
            const x = padding.left + slotWidth * index + slotWidth / 2;
            const y = padding.top + chartHeight -
                (item.value / maxValue) * chartHeight;

            return {
                x: x,
                y: y,
                label: item.label,
                value: item.value,
            };
        });

        // Area fill
        if (points.length > 1) {
            const gradient = ctx.createLinearGradient(
                0,
                padding.top,
                0,
                padding.top + chartHeight
            );

            gradient.addColorStop(0, 'rgba(22, 134, 60, .20)');
            gradient.addColorStop(1, 'rgba(22, 134, 60, .015)');

            ctx.beginPath();
            ctx.moveTo(points[0].x, padding.top + chartHeight);

            points.forEach(function (point, index) {
                if (index === 0) {
                    ctx.lineTo(point.x, point.y);
                    return;
                }

                const previous = points[index - 1];
                const middleX = (previous.x + point.x) / 2;

                ctx.bezierCurveTo(
                    middleX,
                    previous.y,
                    middleX,
                    point.y,
                    point.x,
                    point.y
                );
            });

            ctx.lineTo(points[points.length - 1].x, padding.top + chartHeight);
            ctx.closePath();
            ctx.fillStyle = gradient;
            ctx.fill();
        }

        // Revenue line
        if (points.length > 1) {
            ctx.beginPath();

            points.forEach(function (point, index) {
                if (index === 0) {
                    ctx.moveTo(point.x, point.y);
                    return;
                }

                const previous = points[index - 1];
                const middleX = (previous.x + point.x) / 2;

                ctx.bezierCurveTo(
                    middleX,
                    previous.y,
                    middleX,
                    point.y,
                    point.x,
                    point.y
                );
            });

            ctx.strokeStyle = '#16863c';
            ctx.lineWidth = 2.5;
            ctx.lineJoin = 'round';
            ctx.lineCap = 'round';
            ctx.stroke();
        }

        // Point + x labels
        points.forEach(function (point) {
            ctx.beginPath();
            ctx.arc(point.x, point.y, 5, 0, Math.PI * 2);
            ctx.fillStyle = '#ffffff';
            ctx.fill();
            ctx.strokeStyle = '#16863c';
            ctx.lineWidth = 2.5;
            ctx.stroke();

            ctx.fillStyle = '#748078';
            ctx.font = '10px "Segoe UI", Tahoma, Geneva, Verdana, sans-serif';
            ctx.textAlign = 'center';
            ctx.textBaseline = 'top';
            ctx.fillText(
                labelText(point.label),
                point.x,
                padding.top + chartHeight + 12
            );
        });

        // Nếu chỉ có một điểm, vẽ cột để biểu đồ vẫn trực quan.
        if (points.length === 1) {
            const point = points[0];
            const baseY = padding.top + chartHeight;
            const barWidth = Math.min(80, chartWidth * .28);

            const gradient = ctx.createLinearGradient(0, point.y, 0, baseY);
            gradient.addColorStop(0, '#16863c');
            gradient.addColorStop(1, '#7cc18e');

            ctx.fillStyle = gradient;

            const barHeight = Math.max(4, baseY - point.y);
            const radius = 8;
            const x = point.x - barWidth / 2;
            const y = baseY - barHeight;

            ctx.beginPath();
            ctx.roundRect(x, y, barWidth, barHeight, radius);
            ctx.fill();

            ctx.beginPath();
            ctx.arc(point.x, y, 5, 0, Math.PI * 2);
            ctx.fillStyle = '#ffffff';
            ctx.fill();
            ctx.strokeStyle = '#16863c';
            ctx.lineWidth = 2.5;
            ctx.stroke();

            points[0].y = y;
        }
    }

    function nearestPoint(mouseX, mouseY) {
        let nearest = null;
        let distance = Infinity;

        points.forEach(function (point) {
            const dx = mouseX - point.x;
            const dy = mouseY - point.y;
            const d = Math.sqrt(dx * dx + dy * dy);

            if (d < distance) {
                distance = d;
                nearest = point;
            }
        });

        return distance <= 34 ? nearest : null;
    }

    canvas.addEventListener('mousemove', function (event) {
        if (!tooltip) {
            return;
        }

        const rect = canvas.getBoundingClientRect();
        const x = event.clientX - rect.left;
        const y = event.clientY - rect.top;
        const point = nearestPoint(x, y);

        if (!point) {
            tooltip.hidden = true;
            return;
        }

        tooltip.innerHTML =
            '<span>' + labelText(point.label) + '</span>' +
            '<strong>' + money(point.value) + '</strong>';

        tooltip.style.left = point.x + 'px';
        tooltip.style.top = point.y + 'px';
        tooltip.hidden = false;
    });

    canvas.addEventListener('mouseleave', function () {
        if (tooltip) {
            tooltip.hidden = true;
        }
    });

    function scheduleDraw() {
        if (animationFrame) {
            cancelAnimationFrame(animationFrame);
        }

        animationFrame = requestAnimationFrame(draw);
    }

    if ('ResizeObserver' in window) {
        const observer = new ResizeObserver(scheduleDraw);
        observer.observe(canvas.parentElement);
    } else {
        window.addEventListener('resize', scheduleDraw);
    }

    scheduleDraw();
})();
