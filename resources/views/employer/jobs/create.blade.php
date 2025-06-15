@extends('layouts.app')

@section('title', 'Create New Job')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <h1 class="text-3xl font-bold text-gray-900 mb-8">Create New Job Posting</h1>
        
        <div class="bg-white shadow-lg rounded-lg p-6">
            <form method="POST" action="{{ route('employer.jobs.store') }}">
                @csrf
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="md:col-span-2">
                        <label for="title" class="block text-sm font-medium text-gray-700 mb-2">Job Title</label>
                        <input type="text" id="title" name="title" value="{{ old('title') }}" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" 
                               required>
                    </div>
                    
                    <div>
                        <label for="job_category_id" class="block text-sm font-medium text-gray-700 mb-2">Job Category</label>
                        <select id="job_category_id" name="job_category_id" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" 
                                required>
                            <option value="">Select Category</option>
                        </select>
                    </div>
                    
                    <div>
                        <label for="job_type_id" class="block text-sm font-medium text-gray-700 mb-2">Job Type</label>
                        <select id="job_type_id" name="job_type_id" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" 
                                required>
                            <option value="">Select Type</option>
                        </select>
                    </div>
                    
                    <div>
                        <label for="salary_min" class="block text-sm font-medium text-gray-700 mb-2">Minimum Salary</label>
                        <input type="number" id="salary_min" name="salary_min" value="{{ old('salary_min') }}" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    
                    <div>
                        <label for="salary_max" class="block text-sm font-medium text-gray-700 mb-2">Maximum Salary</label>
                        <input type="number" id="salary_max" name="salary_max" value="{{ old('salary_max') }}" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>
                
                <div class="mt-6">
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Job Description</label>
                    <textarea id="description" name="description" rows="6" 
                              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" 
                              required>{{ old('description') }}</textarea>
                </div>
                
                <div class="mt-6">
                    <label for="requirements" class="block text-sm font-medium text-gray-700 mb-2">Requirements</label>
                    <textarea id="requirements" name="requirements" rows="4" 
                              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old('requirements') }}</textarea>
                </div>
                
                <div class="mt-8 flex justify-end space-x-4">
                    <a href="{{ route('employer.jobs.index') }}" 
                       class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">
                        Cancel
                    </a>
                    <button type="submit" 
                            class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                        Create Job
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection 