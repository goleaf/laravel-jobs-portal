<div class="flex items-center space-x-4">
    <!-- Theme Switcher -->
    <x-theme-switch />

    <!-- Documentation Links -->
    <div class="relative">
        <button id="documentation-dropdown" class="p-1 rounded-full text-gray-600 hover:text-gray-900 focus:outline-none">
            <x-icons.question-mark-circle class="w-6 h-6" />
        </button>
        <div id="documentation-menu" class="hidden absolute right-0 mt-2 w-48 rounded-md shadow-lg py-1 bg-white ring-1 ring-black ring-opacity-5 z-50">
            <a href="{{ route('components.icon-documentation')  }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                Component Documentation
            </a>
            <a href="{{ route('icons.documentation')  }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                Icon System
            </a>
        </div>
    </div>

    <!-- User Dropdown (existing) -->
    <!-- ... existing dropdown code ... -->
</div>

@push('scripts')
<script>
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
</script>
@endpush 