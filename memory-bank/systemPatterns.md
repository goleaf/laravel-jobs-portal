# SYSTEM PATTERNS - LEVEL 4 ENTERPRISE ARCHITECTURE

**Project**: Laravel Job Portal - Level 4 Comprehensive System Transformation  
**Architecture**: Enterprise-Grade Clean Architecture with Domain-Driven Design  
**Patterns**: Modern PHP 8.3, Vue 3 + TypeScript, and Advanced Enterprise Patterns

---

## 🏗️ **ENTERPRISE ARCHITECTURE PATTERNS**

### **Clean Architecture Implementation**
```
┌─────────────────────────────────────────────────────────┐
│                    OUTER LAYER                          │
│  ┌─────────────────────────────────────────────────┐   │
│  │               INTERFACE LAYER                   │   │
│  │  • Vue 3 + TypeScript Components              │   │
│  │  • RESTful API Controllers (Thin)             │   │
│  │  • Request/Response Classes                    │   │
│  │  • Middleware & Guards                         │   │
│  │  ┌─────────────────────────────────────────┐   │   │
│  │  │            APPLICATION LAYER            │   │   │
│  │  │  • Application Services                 │   │   │
│  │  │  • Command/Query Handlers               │   │   │
│  │  │  • Event Handlers & Listeners           │   │   │
│  │  │  • Business Orchestration               │   │   │
│  │  │  ┌─────────────────────────────────┐   │   │   │
│  │  │  │         DOMAIN LAYER           │   │   │   │
│  │  │  │  • Enhanced Models (54)        │   │   │   │
│  │  │  │  • Domain Services             │   │   │   │
│  │  │  │  • Business Rules & Policies   │   │   │   │
│  │  │  │  • Value Objects & Events      │   │   │   │
│  │  │  └─────────────────────────────────┘   │   │   │
│  │  └─────────────────────────────────────────┘   │   │
│  └─────────────────────────────────────────────────┘   │
│                 INFRASTRUCTURE LAYER                   │
│  • Database & ORM Configuration                        │
│  • External API Integrations                           │
│  • Caching & Session Management                        │
│  • File Storage & CDN Services                         │
└─────────────────────────────────────────────────────────┘
```

### **Domain-Driven Design (DDD) Patterns**

#### **Aggregate Root Pattern**
```php
// User Aggregate Root
class User extends Model implements AggregateRoot
{
    // Identity
    protected $primaryKey = 'id';
    
    // Domain Events
    protected $domainEvents = [];
    
    // Business Methods
    public function changeEmail(string $newEmail): void
    {
        $this->validateEmailUniqueness($newEmail);
        $this->email = $newEmail;
        $this->recordDomainEvent(new UserEmailChanged($this));
    }
    
    public function assignRole(Role $role): void
    {
        $this->validateRoleAssignment($role);
        $this->roles()->attach($role);
        $this->recordDomainEvent(new UserRoleAssigned($this, $role));
    }
    
    // Domain Rules
    private function validateEmailUniqueness(string $email): void
    {
        if (static::where('email', $email)->where('id', '!=', $this->id)->exists()) {
            throw new DomainException('Email already exists');
        }
    }
}
```

#### **Value Object Pattern**
```php
// Money Value Object
class Money implements ValueObject
{
    private float $amount;
    private Currency $currency;
    
    public function __construct(float $amount, Currency $currency)
    {
        $this->amount = $amount;
        $this->currency = $currency;
        $this->validate();
    }
    
    public function add(Money $other): Money
    {
        $this->assertSameCurrency($other);
        return new Money($this->amount + $other->amount, $this->currency);
    }
    
    public function equals(ValueObject $other): bool
    {
        return $other instanceof Money
            && $this->amount === $other->amount
            && $this->currency->equals($other->currency);
    }
}

// Location Value Object
class Location implements ValueObject
{
    private string $country;
    private string $state;
    private string $city;
    
    public function __construct(string $country, string $state, string $city)
    {
        $this->country = $country;
        $this->state = $state;
        $this->city = $city;
    }
    
    public function getFullLocation(): string
    {
        return "{$this->city}, {$this->state}, {$this->country}";
    }
}
```

#### **Domain Service Pattern**
```php
// Job Matching Domain Service
class JobMatchingService
{
    public function __construct(
        private SkillMatchingService $skillMatcher,
        private LocationService $locationService,
        private SalaryService $salaryService
    ) {}
    
    public function findMatchingJobs(Candidate $candidate): JobCollection
    {
        $criteria = new JobMatchingCriteria(
            skills: $candidate->getSkills(),
            location: $candidate->getPreferredLocation(),
            salaryRange: $candidate->getExpectedSalaryRange(),
            experienceLevel: $candidate->getExperienceLevel()
        );
        
        return $this->scoreAndRankJobs($criteria);
    }
    
    private function scoreAndRankJobs(JobMatchingCriteria $criteria): JobCollection
    {
        // Complex business logic for job matching
        return Job::query()
            ->withScopes($criteria->getScopes())
            ->orderByMatchScore($criteria)
            ->get();
    }
}
```

---

## 🎯 **MODERN LARAVEL PATTERNS**

### **Enhanced Model Pattern**
```php
// Standard Enhanced Model Template
class EnhancedModel extends Model
{
    use HasFactory, SoftDeletes, HasTranslations, Searchable;
    
    // Modern Casts with Enum Support
    protected function casts(): array
    {
        return [
            'status' => ModelStatus::class,
            'created_at' => 'datetime:Y-m-d H:i:s',
            'updated_at' => 'datetime:Y-m-d H:i:s',
            'deleted_at' => 'datetime:Y-m-d H:i:s',
            'metadata' => 'array',
            'settings' => AsArrayObject::class,
            'is_active' => 'boolean',
            'priority' => 'integer',
        ];
    }
    
    // Translatable Fields
    public array $translatedAttributes = ['name', 'description'];
    
    // Searchable Configuration
    public function toSearchableArray(): array
    {
        return [
            'name' => $this->name,
            'description' => $this->description,
            'status' => $this->status->value,
        ];
    }
    
    // 25+ Comprehensive Scopes Pattern
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }
    
    public function scopeByStatus(Builder $query, ModelStatus $status): Builder
    {
        return $query->where('status', $status);
    }
    
    public function scopeWithinDateRange(Builder $query, Carbon $start, Carbon $end): Builder
    {
        return $query->whereBetween('created_at', [$start, $end]);
    }
    
    public function scopeSearch(Builder $query, string $term): Builder
    {
        return $query->where(function (Builder $q) use ($term) {
            $q->where('name', 'LIKE', "%{$term}%")
              ->orWhere('description', 'LIKE', "%{$term}%");
        });
    }
    
    // Advanced Relationship Patterns
    public function auditLogs(): MorphMany
    {
        return $this->morphMany(AuditLog::class, 'auditable');
    }
    
    public function tags(): MorphToMany
    {
        return $this->morphToMany(Tag::class, 'taggable');
    }
    
    // Business Logic Methods
    public function activate(): void
    {
        $this->update(['is_active' => true, 'status' => ModelStatus::ACTIVE]);
        $this->fireModelEvent('activated', false);
    }
    
    public function deactivate(): void
    {
        $this->update(['is_active' => false, 'status' => ModelStatus::INACTIVE]);
        $this->fireModelEvent('deactivated', false);
    }
}
```

### **Controller Pattern (Thin Controllers)**
```php
// Modern Thin Controller Pattern
class ModelController extends Controller
{
    public function __construct(
        private ModelService $modelService,
        private CacheService $cacheService
    ) {}
    
    public function index(IndexModelRequest $request): ModelCollection
    {
        $models = $this->cacheService->remember(
            key: "models.{$request->getCacheKey()}",
            ttl: 3600,
            callback: fn() => $this->modelService->getPaginated($request->getFilters())
        );
        
        return new ModelCollection($models);
    }
    
    public function show(ShowModelRequest $request, Model $model): ModelResource
    {
        $this->authorize('view', $model);
        
        $model = $this->modelService->getWithRelations($model, $request->getIncludes());
        
        return new ModelResource($model);
    }
    
    public function store(CreateModelRequest $request): ModelResource
    {
        $model = $this->modelService->create($request->validated());
        
        return new ModelResource($model);
    }
    
    public function update(UpdateModelRequest $request, Model $model): ModelResource
    {
        $this->authorize('update', $model);
        
        $model = $this->modelService->update($model, $request->validated());
        
        return new ModelResource($model);
    }
    
    public function destroy(DestroyModelRequest $request, Model $model): JsonResponse
    {
        $this->authorize('delete', $model);
        
        $this->modelService->delete($model);
        
        return response()->json(['message' => __('models.deleted_successfully')]);
    }
}
```

### **Request Validation Pattern**
```php
// Comprehensive Request Pattern with Multilanguage
class CreateModelRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('create', Model::class) ?? false;
    }
    
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'name.*' => ['required', 'string', 'max:255'], // Multilang
            'description' => ['nullable', 'string', 'max:1000'],
            'description.*' => ['nullable', 'string', 'max:1000'], // Multilang
            'status' => ['required', Rule::enum(ModelStatus::class)],
            'priority' => ['nullable', 'integer', 'min:1', 'max:100'],
            'metadata' => ['nullable', 'array'],
            'metadata.*' => ['nullable', 'string'],
            'tags' => ['nullable', 'array'],
            'tags.*' => ['required', 'string', 'exists:tags,name'],
        ];
    }
    
    public function messages(): array
    {
        return [
            'name.required' => __('validation.required', ['attribute' => __('models.name')]),
            'name.*.required' => __('validation.required', ['attribute' => __('models.name')]),
            'status.required' => __('validation.required', ['attribute' => __('models.status')]),
            'status.enum' => __('validation.enum', ['attribute' => __('models.status')]),
        ];
    }
    
    public function attributes(): array
    {
        return [
            'name' => __('models.name'),
            'description' => __('models.description'),
            'status' => __('models.status'),
            'priority' => __('models.priority'),
        ];
    }
    
    protected function prepareForValidation(): void
    {
        $this->merge([
            'status' => ModelStatus::tryFrom($this->status) ?? ModelStatus::DRAFT,
        ]);
    }
}
```

### **Resource Response Pattern**
```php
// Comprehensive Resource Pattern
class ModelResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->getTranslation('name'),
            'description' => $this->getTranslation('description'),
            'status' => [
                'value' => $this->status->value,
                'label' => $this->status->getLabel(),
                'color' => $this->status->getColor(),
            ],
            'priority' => $this->priority,
            'is_active' => $this->is_active,
            'metadata' => $this->metadata,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            
            // Conditional Fields Based on Permissions
            $this->mergeWhen($request->user()?->can('view-admin-data'), [
                'admin_notes' => $this->admin_notes,
                'internal_id' => $this->internal_id,
            ]),
            
            // Conditional Relationships
            $this->mergeWhen($this->relationLoaded('tags'), [
                'tags' => TagResource::collection($this->whenLoaded('tags')),
            ]),
            
            $this->mergeWhen($this->relationLoaded('auditLogs'), [
                'audit_logs' => AuditLogResource::collection($this->whenLoaded('auditLogs')),
            ]),
            
            // Computed Fields
            'display_name' => $this->getDisplayName(),
            'can_edit' => $request->user()?->can('update', $this->resource),
            'can_delete' => $request->user()?->can('delete', $this->resource),
        ];
    }
    
    public function with($request): array
    {
        return [
            'meta' => [
                'available_statuses' => ModelStatus::getSelectOptions(),
                'permissions' => [
                    'can_create' => $request->user()?->can('create', Model::class),
                    'can_view_all' => $request->user()?->can('viewAny', Model::class),
                ],
            ],
        ];
    }
}
```

---

## 🎨 **VUE 3 + TYPESCRIPT PATTERNS**

### **Composition API Component Pattern**
```typescript
// Standard Vue 3 + TypeScript Component Pattern
<script setup lang="ts">
import { ref, reactive, computed, onMounted, watch } from 'vue';
import { useI18n } from 'vue-i18n';
import { useRouter } from 'vue-router';
import { storeToRefs } from 'pinia';
import { useModelStore } from '@/stores/modelStore';
import type { Model, ModelFilters, PaginationMeta } from '@/types/models';

// Props with TypeScript
interface Props {
  initialFilters?: ModelFilters;
  readonly?: boolean;
  showActions?: boolean;
}

const props = withDefaults(defineProps<Props>(), {
  initialFilters: () => ({}),
  readonly: false,
  showActions: true,
});

// Emits with TypeScript
interface Emits {
  (e: 'model-selected', model: Model): void;
  (e: 'filters-changed', filters: ModelFilters): void;
}

const emit = defineEmits<Emits>();

// Composables
const { t } = useI18n();
const router = useRouter();
const modelStore = useModelStore();

// Store state
const { models, loading, pagination } = storeToRefs(modelStore);

// Local reactive state
const filters = reactive<ModelFilters>({
  search: '',
  status: null,
  dateRange: null,
  ...props.initialFilters,
});

const selectedModels = ref<Model[]>([]);
const showFilters = ref(false);

// Computed properties
const filteredModels = computed(() => {
  return models.value.filter(model => {
    if (filters.search && !model.name.toLowerCase().includes(filters.search.toLowerCase())) {
      return false;
    }
    if (filters.status && model.status !== filters.status) {
      return false;
    }
    return true;
  });
});

const hasFilters = computed(() => {
  return Object.values(filters).some(value => value !== null && value !== '');
});

const canCreateModel = computed(() => {
  return !props.readonly && modelStore.permissions.canCreate;
});

// Methods
const fetchModels = async (page = 1): Promise<void> => {
  try {
    await modelStore.fetchModels({ ...filters, page });
  } catch (error) {
    console.error('Failed to fetch models:', error);
  }
};

const handleModelSelect = (model: Model): void => {
  emit('model-selected', model);
};

const handleFiltersChange = (): void => {
  emit('filters-changed', { ...filters });
  fetchModels(1);
};

const clearFilters = (): void => {
  Object.keys(filters).forEach(key => {
    filters[key as keyof ModelFilters] = null;
  });
  handleFiltersChange();
};

const handleCreateModel = (): void => {
  router.push({ name: 'models.create' });
};

// Watchers
watch(
  () => props.initialFilters,
  (newFilters) => {
    Object.assign(filters, newFilters);
    handleFiltersChange();
  },
  { deep: true }
);

// Lifecycle
onMounted(() => {
  fetchModels();
});
</script>

<template>
  <div class="model-management">
    <!-- Header -->
    <div class="mb-6 flex items-center justify-between">
      <h1 class="text-2xl font-bold text-gray-900">
        {{ t('models.title') }}
      </h1>
      
      <div class="flex space-x-3">
        <button
          v-if="canCreateModel"
          @click="handleCreateModel"
          class="btn btn-primary"
        >
          <PlusIcon class="h-5 w-5 mr-2" />
          {{ t('models.create') }}
        </button>
      </div>
    </div>
    
    <!-- Filters -->
    <ModelFilters
      v-model="filters"
      :show="showFilters"
      @change="handleFiltersChange"
      @clear="clearFilters"
    />
    
    <!-- Models Table -->
    <ModelTable
      :models="filteredModels"
      :loading="loading"
      :pagination="pagination"
      :selected="selectedModels"
      :readonly="readonly"
      :show-actions="showActions"
      @select="handleModelSelect"
      @page-change="fetchModels"
    />
  </div>
</template>
```

### **Pinia Store Pattern**
```typescript
// Modern Pinia Store Pattern
import { defineStore } from 'pinia';
import { ref, computed } from 'vue';
import type { Model, ModelFilters, CreateModelData, UpdateModelData } from '@/types/models';
import { modelApi } from '@/services/api/modelApi';

export const useModelStore = defineStore('models', () => {
  // State
  const models = ref<Model[]>([]);
  const currentModel = ref<Model | null>(null);
  const loading = ref(false);
  const error = ref<string | null>(null);
  const pagination = ref({
    current_page: 1,
    per_page: 15,
    total: 0,
    last_page: 1,
  });
  
  // Getters (computed)
  const activeModels = computed(() => 
    models.value.filter(model => model.is_active)
  );
  
  const modelsByStatus = computed(() => {
    const grouped: Record<string, Model[]> = {};
    models.value.forEach(model => {
      if (!grouped[model.status]) {
        grouped[model.status] = [];
      }
      grouped[model.status].push(model);
    });
    return grouped;
  });
  
  const permissions = computed(() => ({
    canCreate: true, // Get from auth store
    canEdit: true,
    canDelete: true,
    canViewAll: true,
  }));
  
  // Actions
  const fetchModels = async (filters: ModelFilters = {}): Promise<void> => {
    loading.value = true;
    error.value = null;
    
    try {
      const response = await modelApi.getModels(filters);
      models.value = response.data;
      pagination.value = response.meta;
    } catch (err) {
      error.value = err instanceof Error ? err.message : 'Unknown error';
      throw err;
    } finally {
      loading.value = false;
    }
  };
  
  const fetchModel = async (id: number): Promise<Model> => {
    loading.value = true;
    
    try {
      const model = await modelApi.getModel(id);
      currentModel.value = model;
      return model;
    } catch (err) {
      error.value = err instanceof Error ? err.message : 'Unknown error';
      throw err;
    } finally {
      loading.value = false;
    }
  };
  
  const createModel = async (data: CreateModelData): Promise<Model> => {
    loading.value = true;
    
    try {
      const model = await modelApi.createModel(data);
      models.value.unshift(model);
      return model;
    } catch (err) {
      error.value = err instanceof Error ? err.message : 'Unknown error';
      throw err;
    } finally {
      loading.value = false;
    }
  };
  
  const updateModel = async (id: number, data: UpdateModelData): Promise<Model> => {
    loading.value = true;
    
    try {
      const model = await modelApi.updateModel(id, data);
      const index = models.value.findIndex(m => m.id === id);
      if (index !== -1) {
        models.value[index] = model;
      }
      if (currentModel.value?.id === id) {
        currentModel.value = model;
      }
      return model;
    } catch (err) {
      error.value = err instanceof Error ? err.message : 'Unknown error';
      throw err;
    } finally {
      loading.value = false;
    }
  };
  
  const deleteModel = async (id: number): Promise<void> => {
    loading.value = true;
    
    try {
      await modelApi.deleteModel(id);
      models.value = models.value.filter(m => m.id !== id);
      if (currentModel.value?.id === id) {
        currentModel.value = null;
      }
    } catch (err) {
      error.value = err instanceof Error ? err.message : 'Unknown error';
      throw err;
    } finally {
      loading.value = false;
    }
  };
  
  const clearError = (): void => {
    error.value = null;
  };
  
  const resetState = (): void => {
    models.value = [];
    currentModel.value = null;
    loading.value = false;
    error.value = null;
    pagination.value = {
      current_page: 1,
      per_page: 15,
      total: 0,
      last_page: 1,
    };
  };
  
  return {
    // State
    models,
    currentModel,
    loading,
    error,
    pagination,
    
    // Getters
    activeModels,
    modelsByStatus,
    permissions,
    
    // Actions
    fetchModels,
    fetchModel,
    createModel,
    updateModel,
    deleteModel,
    clearError,
    resetState,
  };
});
```

---

## 🧪 **TESTING PATTERNS**

### **Model Testing Pattern**
```php
// Comprehensive Model Test Pattern
class ModelTest extends TestCase
{
    use RefreshDatabase, WithFaker;
    
    private Model $model;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->model = Model::factory()->create();
    }
    
    /** @test */
    public function it_can_create_model_with_valid_data(): void
    {
        $data = Model::factory()->make()->toArray();
        $model = Model::create($data);
        
        $this->assertInstanceOf(Model::class, $model);
        $this->assertDatabaseHas('models', $data);
        $this->assertTrue($model->is_active);
    }
    
    /** @test */
    public function it_has_required_fillable_fields(): void
    {
        $fillable = ['name', 'description', 'status', 'priority', 'metadata'];
        $this->assertEquals($fillable, $this->model->getFillable());
    }
    
    /** @test */
    public function it_casts_attributes_correctly(): void
    {
        $this->assertInstanceOf(ModelStatus::class, $this->model->status);
        $this->assertInstanceOf(Carbon::class, $this->model->created_at);
        $this->assertIsBool($this->model->is_active);
        $this->assertIsArray($this->model->metadata);
    }
    
    /** @test */
    public function it_has_active_scope(): void
    {
        Model::factory(3)->create(['is_active' => true]);
        Model::factory(2)->create(['is_active' => false]);
        
        $activeModels = Model::active()->get();
        
        $this->assertCount(4, $activeModels); // 3 + setUp model
        $this->assertTrue($activeModels->every(fn($model) => $model->is_active));
    }
    
    /** @test */
    public function it_has_status_scope(): void
    {
        Model::factory(3)->create(['status' => ModelStatus::ACTIVE]);
        Model::factory(2)->create(['status' => ModelStatus::INACTIVE]);
        
        $activeModels = Model::byStatus(ModelStatus::ACTIVE)->get();
        
        $this->assertCount(3, $activeModels);
        $this->assertTrue($activeModels->every(fn($model) => $model->status === ModelStatus::ACTIVE));
    }
    
    /** @test */
    public function it_can_be_activated(): void
    {
        $this->model->update(['is_active' => false, 'status' => ModelStatus::INACTIVE]);
        
        $this->model->activate();
        
        $this->assertTrue($this->model->is_active);
        $this->assertEquals(ModelStatus::ACTIVE, $this->model->status);
    }
    
    /** @test */
    public function it_can_be_deactivated(): void
    {
        $this->model->update(['is_active' => true, 'status' => ModelStatus::ACTIVE]);
        
        $this->model->deactivate();
        
        $this->assertFalse($this->model->is_active);
        $this->assertEquals(ModelStatus::INACTIVE, $this->model->status);
    }
    
    /** @test */
    public function it_has_audit_logs_relationship(): void
    {
        $this->assertInstanceOf(MorphMany::class, $this->model->auditLogs());
    }
    
    /** @test */
    public function it_has_tags_relationship(): void
    {
        $this->assertInstanceOf(MorphToMany::class, $this->model->tags());
    }
    
    /** @test */
    public function it_implements_searchable_interface(): void
    {
        $this->assertContains(Searchable::class, class_uses_recursive(Model::class));
    }
    
    /** @test */
    public function it_returns_correct_searchable_array(): void
    {
        $searchableArray = $this->model->toSearchableArray();
        
        $this->assertArrayHasKey('name', $searchableArray);
        $this->assertArrayHasKey('description', $searchableArray);
        $this->assertArrayHasKey('status', $searchableArray);
    }
}
```

### **Controller Testing Pattern**
```php
// Comprehensive Controller Test Pattern
class ModelControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;
    
    private User $user;
    private Model $model;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->model = Model::factory()->create();
    }
    
    /** @test */
    public function it_can_list_models(): void
    {
        Model::factory(5)->create();
        
        $response = $this->actingAs($this->user)
            ->getJson('/api/models');
        
        $response->assertOk()
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'name', 'description', 'status', 'created_at']
                ],
                'meta' => ['current_page', 'per_page', 'total', 'last_page']
            ]);
    }
    
    /** @test */
    public function it_can_show_single_model(): void
    {
        $response = $this->actingAs($this->user)
            ->getJson("/api/models/{$this->model->id}");
        
        $response->assertOk()
            ->assertJson([
                'data' => [
                    'id' => $this->model->id,
                    'name' => $this->model->name,
                ]
            ]);
    }
    
    /** @test */
    public function it_can_create_model(): void
    {
        $data = [
            'name' => ['en' => 'Test Model', 'fr' => 'Modèle de test'],
            'description' => ['en' => 'Test Description'],
            'status' => ModelStatus::ACTIVE->value,
            'priority' => 5,
        ];
        
        $response = $this->actingAs($this->user)
            ->postJson('/api/models', $data);
        
        $response->assertCreated()
            ->assertJsonFragment(['name' => 'Test Model']);
        
        $this->assertDatabaseHas('models', [
            'name->en' => 'Test Model',
            'status' => ModelStatus::ACTIVE->value,
        ]);
    }
    
    /** @test */
    public function it_validates_required_fields_on_create(): void
    {
        $response = $this->actingAs($this->user)
            ->postJson('/api/models', []);
        
        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['name', 'status']);
    }
    
    /** @test */
    public function it_can_update_model(): void
    {
        $data = [
            'name' => ['en' => 'Updated Model'],
            'status' => ModelStatus::INACTIVE->value,
        ];
        
        $response = $this->actingAs($this->user)
            ->putJson("/api/models/{$this->model->id}", $data);
        
        $response->assertOk();
        
        $this->model->refresh();
        $this->assertEquals('Updated Model', $this->model->getTranslation('name', 'en'));
        $this->assertEquals(ModelStatus::INACTIVE, $this->model->status);
    }
    
    /** @test */
    public function it_can_delete_model(): void
    {
        $response = $this->actingAs($this->user)
            ->deleteJson("/api/models/{$this->model->id}");
        
        $response->assertOk();
        $this->assertSoftDeleted('models', ['id' => $this->model->id]);
    }
    
    /** @test */
    public function it_requires_authentication(): void
    {
        $response = $this->getJson('/api/models');
        $response->assertUnauthorized();
    }
}
```

### **Vue Component Testing Pattern**
```typescript
// Vue Component Test Pattern
import { mount } from '@vue/test-utils';
import { describe, it, expect, beforeEach, vi } from 'vitest';
import { createTestingPinia } from '@pinia/testing';
import ModelManagement from '@/components/ModelManagement.vue';
import { useModelStore } from '@/stores/modelStore';

describe('ModelManagement', () => {
  let wrapper: any;
  let mockStore: any;
  
  beforeEach(() => {
    wrapper = mount(ModelManagement, {
      global: {
        plugins: [
          createTestingPinia({
            createSpy: vi.fn,
          })
        ],
      },
    });
    
    mockStore = useModelStore();
  });
  
  it('renders the component', () => {
    expect(wrapper.find('.model-management').exists()).toBe(true);
    expect(wrapper.find('h1').text()).toContain('Models');
  });
  
  it('fetches models on mount', () => {
    expect(mockStore.fetchModels).toHaveBeenCalled();
  });
  
  it('displays models in table', async () => {
    const mockModels = [
      { id: 1, name: 'Model 1', status: 'active' },
      { id: 2, name: 'Model 2', status: 'inactive' },
    ];
    
    mockStore.models = mockModels;
    await wrapper.vm.$nextTick();
    
    expect(wrapper.findAll('[data-testid="model-row"]')).toHaveLength(2);
  });
  
  it('filters models by search term', async () => {
    const searchInput = wrapper.find('[data-testid="search-input"]');
    await searchInput.setValue('Model 1');
    
    expect(mockStore.fetchModels).toHaveBeenCalledWith(
      expect.objectContaining({ search: 'Model 1' })
    );
  });
  
  it('emits model-selected event when model is clicked', async () => {
    const model = { id: 1, name: 'Test Model' };
    
    await wrapper.vm.handleModelSelect(model);
    
    expect(wrapper.emitted('model-selected')).toBeTruthy();
    expect(wrapper.emitted('model-selected')[0]).toEqual([model]);
  });
});
```

---

## 🔒 **SECURITY PATTERNS**

### **Authorization Policy Pattern**
```php
// Comprehensive Policy Pattern
class ModelPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('models.view') || $user->isAdmin();
    }
    
    public function view(User $user, Model $model): bool
    {
        return $this->viewAny($user) || $this->owns($user, $model);
    }
    
    public function create(User $user): bool
    {
        return $user->hasPermission('models.create') && $user->isActive();
    }
    
    public function update(User $user, Model $model): bool
    {
        return ($user->hasPermission('models.update') || $this->owns($user, $model))
            && $user->isActive();
    }
    
    public function delete(User $user, Model $model): bool
    {
        return $user->hasPermission('models.delete') && $user->isActive();
    }
    
    private function owns(User $user, Model $model): bool
    {
        return $model->user_id === $user->id;
    }
}
```

### **Rate Limiting Pattern**
```php
// Advanced Rate Limiting
class RateLimitingMiddleware
{
    public function handle(Request $request, Closure $next, string $maxAttempts = '60', string $decayMinutes = '1')
    {
        $key = $this->resolveRequestSignature($request);
        
        if (RateLimiter::tooManyAttempts($key, $maxAttempts)) {
            throw new ThrottleRequestsException();
        }
        
        RateLimiter::hit($key, $decayMinutes * 60);
        
        $response = $next($request);
        
        return $this->addHeaders(
            $response,
            $maxAttempts,
            RateLimiter::retriesLeft($key, $maxAttempts)
        );
    }
    
    protected function resolveRequestSignature(Request $request): string
    {
        return sha1(
            $request->method() .
            '|' . $request->getHost() .
            '|' . $request->path() .
            '|' . ($request->user()?->id ?? $request->ip())
        );
    }
}
```

---

## 📊 **PERFORMANCE PATTERNS**

### **Caching Strategy Pattern**
```php
// Multi-Layer Caching Pattern
class CacheService
{
    private const DEFAULT_TTL = 3600; // 1 hour
    private const TAGS = ['models'];
    
    public function remember(string $key, int $ttl = self::DEFAULT_TTL, callable $callback): mixed
    {
        return Cache::tags(self::TAGS)->remember($key, $ttl, $callback);
    }
    
    public function rememberForever(string $key, callable $callback): mixed
    {
        return Cache::tags(self::TAGS)->rememberForever($key, $callback);
    }
    
    public function forget(string $key): void
    {
        Cache::tags(self::TAGS)->forget($key);
    }
    
    public function flush(): void
    {
        Cache::tags(self::TAGS)->flush();
    }
    
    public function invalidateModelCache(Model $model): void
    {
        $this->forget("model.{$model->id}");
        $this->forget("models.list.*");
        $this->forget("models.search.*");
    }
}
```

### **Database Optimization Pattern**
```php
// Query Optimization Pattern
class OptimizedModelRepository
{
    public function getPaginatedWithOptimization(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return Model::query()
            ->select(['id', 'name', 'status', 'created_at']) // Only needed fields
            ->when($filters['search'] ?? null, function (Builder $query, string $search) {
                $query->where('name', 'LIKE', "%{$search}%")
                      ->orWhere('description', 'LIKE', "%{$search}%");
            })
            ->when($filters['status'] ?? null, function (Builder $query, string $status) {
                $query->where('status', $status);
            })
            ->with(['tags:id,name']) // Eager load with specific columns
            ->withCount(['relatedModels']) // Use withCount instead of loading relations
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }
}
```

**STATUS: COMPREHENSIVE LEVEL 4 ENTERPRISE ARCHITECTURE PATTERNS ESTABLISHED** ⚡

These patterns ensure:
- **Clean Architecture**: Proper separation of concerns
- **Modern PHP**: Latest Laravel and PHP 8.3 features
- **Type Safety**: Full TypeScript integration
- **Security**: Enterprise-grade security patterns
- **Performance**: Optimized queries and caching
- **Testing**: Comprehensive test coverage
- **Maintainability**: Clear, documented, and reusable patterns 

# System Patterns: Laravel Job Portal Modernization

## Core Patterns
- **Enhanced Patterns**: Used for all implementations to ensure consistency and scalability.
- **Modular Architecture**: Code organized into distinct modules for maintainability.
- **RESTful API Design**: Consistent resource-based routing and response structures.

## Model & Controller Patterns
- **Scopes and Casts**: Comprehensive scopes and casts in every model.
- **Single Responsibility**: Controller methods handle specific actions with logic delegation.
- **Request Validation**: Dedicated Form Request classes for each controller action.

## UI/UX Patterns
- **Vue 3 Components**: Reusable frontend elements with TypeScript.
- **TailwindCSS**: Utility-first CSS framework, managed locally via npm.
- **Responsive & Accessible**: Mobile-first design with ARIA attributes.

## Multilingual Patterns
- **JSON Translations**: Strings managed in JSON files for 9 languages with RTL support.
- **Dynamic Switching**: Enhanced I18n integrated with Laravel localization.

## Testing & Performance Patterns
- **Comprehensive Testing**: Aim for 95%+ coverage with unit, feature, and integration tests.
- **Caching & Optimization**: Redis caching and optimized Eloquent queries for performance.
- **Concurrent Support**: Designed for 1000+ concurrent users.

These patterns are aligned with Enhanced best practices and Laravel 12 standards to ensure the job portal transformation results in a modern, efficient, and user-friendly system. 