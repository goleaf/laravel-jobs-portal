<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'Job Portal') }}</title></head>
<body>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mx-auto px-4 mx-auto">
        <div class="flex-wrap flex justify-center">
            <div class="flex-1 md-8">
                <div class="text-center py-5">
                    <h1 class="display-4">Welcome to Job Portal</h1>
                    <p class="lead">Find your dream job or hire the perfect candidate</p>
                    
                    <div class="flex-wrap mt-5 flex">
                        <div class="flex-1 md-6">
                            <div class="overflow-hidden shadow rounded bg-white -lg">
                                <div class="overflow-hidden shadow rounded bg-white -lg body">
                                    <h5 class="overflow-hidden shadow rounded bg-white -lg title">For Job Seekers</h5>
                                    <p class="overflow-hidden shadow rounded bg-white -lg text">Bflex flex-wrap -mx-4se thousands of job opportunities</p>
                                    <a href="{{ route('jobs.index') }}" class="border border-gray-300 bg-transparent">Bflex flex-wrap -mx-4se Jobs</a>
                                </div>
                            </div>
                        </div>
                        <div class="flex-1 md-6">
                            <div class="overflow-hidden shadow rounded bg-white -lg">
                                <div class="overflow-hidden shadow rounded bg-white -lg body">
                                    <h5 class="overflow-hidden shadow rounded bg-white -lg title">For Employers</h5>
                                    <p class="overflow-hidden shadow rounded bg-white -lg text">Find the perfect candidates for your company</p>
                                    <a href="{{ route('companies.index') }}" class="border border-gray-300 bg-transparent">Post a Job</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-4">
                        <a href="{{ route('login') }}" class="border border-gray-300 bg-transparent">Login</a>
                        <a href="{{ route('register') }}" class="border border-gray-300 bg-transparent">Register</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
