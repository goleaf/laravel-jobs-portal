<template>
  <span v-if="iconComponent" :class="['svg-icon', $attrs.class]" v-html="iconComponent" aria-hidden="true" />
  <span v-else class="svg-icon-missing">?</span>
</template>

<script>
export default {
  name: 'SvgIcon',
  props: {
    name: {
      type: String,
      required: true
    }
  },
  computed: {
    iconComponent() {
      // Add more icons as needed
      const icons = {
        search: `<svg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='currentColor'><circle cx='11' cy='11' r='7' stroke-width='2'/><line x1='21' y1='21' x2='16.65' y2='16.65' stroke-width='2'/></svg>`,
        edit: `<svg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='currentColor'><path d='M15.232 5.232l3.536 3.536M9 13l6-6 3 3-6 6H9v-3z' stroke-width='2'/></svg>`,
        delete: `<svg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='currentColor'><rect x='5' y='7' width='14' height='12' rx='2' stroke-width='2'/><path d='M9 7V4h6v3' stroke-width='2'/></svg>`,
        arrow: `<svg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='currentColor'><path d='M5 12h14M12 5l7 7-7 7' stroke-width='2'/></svg>`,
        plus: `<svg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='currentColor'><line x1='12' y1='5' x2='12' y2='19' stroke-width='2'/><line x1='5' y1='12' x2='19' y2='12' stroke-width='2'/></svg>`,
        minus: `<svg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='currentColor'><line x1='5' y1='12' x2='19' y2='12' stroke-width='2'/></svg>`
      };
      return icons[this.name] || null;
    }
  }
};
</script>

<style scoped>
.svg-icon {
  display: inline-block;
  width: 1.25em;
  height: 1.25em;
  vertical-align: middle;
  color: inherit;
}
.svg-icon-missing {
  color: #e53e3e;
  font-weight: bold;
}
</style> 