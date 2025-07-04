<template>
  <div class="overflow-x-auto">
    <table 
      :class="[
        'min-w-full divide-y divide-gray-200',
        {
          'table-fixed': fixedLayout,
          'border border-gray-200': bordered
        }
      ]"
    >
      <!-- Table Header -->
      <thead 
        :class="[
          'bg-gray-50',
          headerClass
        ]"
      >
        <tr>
          <th 
            v-for="(column, index) in columns" 
            :key="index"
            scope="col"
            :class="[
              'px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider',
              column.headerClass || ''
            ]"
            :style="column.width ? `width: ${column.width}` : ''"
          >
            {{ column.label }}
          </th>
        </tr>
      </thead>

      <!-- Table Body -->
      <tbody 
        class="bg-white divide-y divide-gray-200"
      >
        <tr 
          v-for="(item, rowIndex) in data" 
          :key="rowIndex"
          :class="[
            'hover:bg-gray-50 transition-colors duration-200',
            {
              'cursor-pointer': rowClickable
            }
          ]"
          @click="$emit('row-click', item)"
        >
          <td 
            v-for="(column, colIndex) in columns" 
            :key="colIndex"
            :class="[
              'px-6 py-4 whitespace-nowrap',
              column.cellClass || ''
            ]"
          >
            <slot 
              :name="`cell-${column.key}`" 
              :row="item" 
              :value="item[column.key]"
            >
              {{ item[column.key] }}
            </slot>
          </td>
        </tr>
      </tbody>
    </table>

    <!-- Empty State -->
    <div 
      v-if="data.length === 0" 
      class="text-center py-8 text-gray-500"
    >
      <slot name="empty">
        No data available
      </slot>
    </div>
  </div>
</template>

<script setup lang="ts">
interface Column {
  key: string
  label: string
  width?: string
  headerClass?: string
  cellClass?: string
}

interface Props {
  columns: Column[]
  data: any[]
  fixedLayout?: boolean
  bordered?: boolean
  headerClass?: string
  rowClickable?: boolean
}

const props = withDefaults(defineProps<Props>(), {
  fixedLayout: false,
  bordered: false,
  headerClass: '',
  rowClickable: false
})

defineEmits(['row-click'])
</script>

<style scoped>
/* Additional scoped styles if needed */
</style> 