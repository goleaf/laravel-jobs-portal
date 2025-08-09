// Advanced search page behavior
document.addEventListener('DOMContentLoaded', () => {
  const searchType = document.getElementById('search_type');
  const jobFilters = document.getElementById('job-filters');
  const candidateFilters = document.getElementById('candidate-filters');
  const companyFilters = document.getElementById('company-filters');

  function updateSections() {
    const type = searchType ? searchType.value : 'jobs';
    if (jobFilters) jobFilters.style.display = type === 'jobs' ? 'block' : 'none';
    if (candidateFilters) candidateFilters.style.display = type === 'candidates' ? 'block' : 'none';
    if (companyFilters) companyFilters.style.display = type === 'companies' ? 'block' : 'none';
  }

  if (searchType) {
    searchType.addEventListener('change', updateSections);
  }

  // Radius display
  const radius = document.getElementById('radius');
  const display = document.getElementById('radius-display');
  if (radius && display) {
    radius.addEventListener('input', () => {
      display.textContent = `${radius.value} km`;
    });
  }

  // Skills chips management
  const skillsContainer = document.getElementById('skills-input');
  const skillInput = document.getElementById('skill-input');
  window.addSkillFromInput = () => {
    const value = (skillInput?.value || '').trim();
    if (!value || !skillsContainer) return;
    const wrapper = document.createElement('div');
    wrapper.className = 'skill-tag flex items-center';
    wrapper.innerHTML = `
      <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
        ${value}
        <button type="button" class="ml-2 text-blue-600 hover:text-blue-500" data-action="remove-skill">
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="h-3 w-3"><path d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
      </span>
      <input type="hidden" name="skills[]" value="${value}">
    `;
    skillsContainer.appendChild(wrapper);
    skillInput.value = '';
  };

  document.body.addEventListener('click', (e) => {
    const removeBtn = e.target.closest('[data-action="remove-skill"]');
    if (removeBtn) {
      const tag = removeBtn.closest('.skill-tag');
      if (tag) tag.remove();
    }
  });

  window.clearAllFilters = () => {
    const form = document.getElementById('advanced-search-form');
    if (!form) return;
    form.reset();
    updateSections();
    if (skillsContainer) skillsContainer.innerHTML = '';
  };

  window.saveSearch = () => {
    const modal = document.getElementById('save-search-modal');
    if (modal) modal.classList.remove('hidden');
    const paramsInput = document.getElementById('search-params-input');
    const form = document.getElementById('advanced-search-form');
    if (paramsInput && form) {
      const data = new FormData(form);
      const params = new URLSearchParams();
      for (const [key, value] of data.entries()) {
        if (value !== '') params.append(key, value);
      }
      paramsInput.value = params.toString();
    }
  };

  updateSections();
});

