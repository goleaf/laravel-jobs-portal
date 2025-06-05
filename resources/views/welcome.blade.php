<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'Job Portal') }}</title></head>
<body>
    <div class="container mx-auto px-4 mx-auto">
        <div class="flex flex-wrap justify-center">
            <div class="flex-1 md-8">
                <div class="text-center py-5">
                    <h1 class="display-4">Welcome to Job Portal</h1>
                    <p class="lead">Find your dream job or hire the perfect candidate</p>
                    
                    <div class="flex flex-wrap mt-5">
                        <div class="flex-1 md-6">
                            <div class="bg-white shadow rounded-lg overflow-hidden">
                                <div class="bg-white shadow rounded-lg overflow-hidden body">
                                    <h5 class="bg-white shadow rounded-lg overflow-hidden title">For Job Seekers</h5>
                                    <p class="bg-white shadow rounded-lg overflow-hidden text">Browse thousands of job opportunities</p>
                                    <a href="{{ route('jobs.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 border-transparent text-sm font-medium rounded-md transition duration-150 ease-in-out px-4 py-2 rounded font-medium transition-colors primary">Browse Jobs</a>
                                </div>
                            </div>
                        </div>
                        <div class="flex-1 md-6">
                            <div class="bg-white shadow rounded-lg overflow-hidden">
                                <div class="bg-white shadow rounded-lg overflow-hidden body">
                                    <h5 class="bg-white shadow rounded-lg overflow-hidden title">For Employers</h5>
                                    <p class="bg-white shadow rounded-lg overflow-hidden text">Find the perfect candidates for your company</p>
                                    <a href="{{ route('companies.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 border-transparent text-sm font-medium rounded-md transition duration-150 ease-in-out px-4 py-2 rounded font-medium transition-colors success">Post a Job</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-4">
                        <a href="{{ route('login') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 border-transparent text-sm font-medium rounded-md transition duration-150 ease-in-out px-4 py-2 rounded font-medium transition-colors outline-primary me-2">Login</a>
                        <a href="{{ route('register') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 border-transparent text-sm font-medium rounded-md transition duration-150 ease-in-out px-4 py-2 rounded font-medium transition-colors outline-secondary">Register</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
