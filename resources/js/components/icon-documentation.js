document.addEventListener('DOMContentLoaded', function() {
// Component-specific JavaScript
// Search functionality
        document.getElementById('iconSearch').addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            filterIcons();
        });

        // Category filtering
        document.querySelectorAll('.category-filter').forEach(button => {
            button.addEventListener('click', function() {
                // Update active button
                document.querySelectorAll('.category-filter').forEach(btn => {
                    btn.classList.remove('btn-primary');
                    btn.classList.add('btn-outline-secondary');
                });
                this.classList.remove('btn-outline-secondary');
                this.classList.add('btn-primary');
                
                filterIcons();
            });
        });

        function filterIcons() {
            const searchTerm = document.getElementById('iconSearch').value.toLowerCase();
            const activeCategory = document.querySelector('.category-filter.btn-primary')?.dataset.category || 'all';
            const iconItems = document.querySelectorAll('.icon-item');
            let visibleCount = 0;

            iconItems.forEach(item => {
                const iconName = item.dataset.name;
                const iconCategory = item.dataset.category;
                
                const matchesSearch = iconName.includes(searchTerm);
                const matchesCategory = activeCategory === 'all' || iconCategory === activeCategory;
                
                if (matchesSearch && matchesCategory) {
                    item.style.display = 'block';
                    visibleCount++;
                } else {
                    item.style.display = 'none';
                }
            });

            // Show/hide no results message
            document.getElementById('noResults').style.display = visibleCount === 0 ? 'block' : 'none';
        }

        // Copy to clipboard functionality
        function copyToClipboard(iconClass) {
            const textToCopy = `<i class="${iconClass}"></i>`;
            navigator.clipboard.writeText(textToCopy).then(() => {
                const toast = new bootstrap.Toast(document.getElementById('copyToast'));
                toast.show();
            }).catch(err => {
                console.error('Failed to copy: ', err);
                // Fallback for older browsers
                const textArea = document.createElement('textarea');
                textArea.value = textToCopy;
                document.body.appendChild(textArea);
                textArea.select();
                document.execCommand('copy');
                document.body.removeChild(textArea);
                
                const toast = new bootstrap.Toast(document.getElementById('copyToast'));
                toast.show();
            });
        }

        // Initialize with all category active
        document.querySelector('.category-filter[data-category="all"]').click();


});