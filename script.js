function toggleDetails(id) {
    const section = document.getElementById(`details-${id}`);
    if (section.style.display === 'none') {
        section.style.display = 'block';
    } else {
        section.style.display = 'none';
    }
}

function editCartridge(id) {
    alert(`Edit Cartridge ID: ${id} (feature to be implemented)`);
    // You can redirect to edit.php?id=ID here
}

function deleteCartridge(id) {
    if (confirm("Are you sure you want to delete this cartridge?")) {
        window.location.href = `delete_cartridge.php?id=${id}`;
    }
}
