document.addEventListener('DOMContentLoaded', function() {
// Component-specific JavaScript
// Toggle documentation dropdown
    const documentationButton = document.getElementById('documentation-dropdown');
    const documentationMenu = document.getElementById('documentation-menu');
    
    documentationButton.addEventListener('click', () => {
        documentationMenu.classList.toggle('hidden');
    });
    
    // Close the dropdown when clicking outside
    document.addEventListener('click', (event) => {
        if (!documentationButton.contains(event.target) && !documentationMenu.contains(event.target)) {
            documentationMenu.classList.add('hidden');
        }
    });


});