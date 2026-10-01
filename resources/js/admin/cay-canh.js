const config = document.getElementById('plantPageConfig');
const baseUrl = config?.dataset.baseUrl || '/admin/cay-canh';

const plantSuccessAlert = document.getElementById('plantSuccessAlert');
if (plantSuccessAlert) {
    window.setTimeout(() => {
        plantSuccessAlert.classList.add('plant-alert-hiding');
        window.setTimeout(() => plantSuccessAlert.remove(), 300);
    }, 3000);
}

window.openDeletePlantModal = function openDeletePlantModal(plantId, plantName) {
    const modal = document.getElementById('deletePlantModal');
    const name = document.getElementById('deletePlantName');
    const form = document.getElementById('deletePlantForm');
    if (!modal || !name || !form) {
        console.error('Không tìm thấy modal xóa cây.');
        return;
    }
    name.textContent = `"${plantName}"`;
    form.action = `${baseUrl}/${plantId}`;
    modal.classList.add('show');
    document.body.style.overflow = 'hidden';
};

window.closeDeletePlantModal = function closeDeletePlantModal() {
    const modal = document.getElementById('deletePlantModal');
    if (!modal) return;
    modal.classList.remove('show');
    document.body.style.overflow = '';
};

document.addEventListener('keydown', (event) => {
    if (event.key === 'Escape') window.closeDeletePlantModal();
});
