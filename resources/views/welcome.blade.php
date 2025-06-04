<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'Job Portal') }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="text-center py-5">
                    <h1 class="display-4">Welcome to Job Portal</h1>
                    <p class="lead">Find your dream job or hire the perfect candidate</p>
                    
                    <div class="row mt-5">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-body">
                                    <h5 class="card-title">For Job Seekers</h5>
                                    <p class="card-text">Browse thousands of job opportunities</p>
                                    <a href="{{ route('jobs.index') }}" class="btn btn-primary">Browse Jobs</a>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-body">
                                    <h5 class="card-title">For Employers</h5>
                                    <p class="card-text">Find the perfect candidates for your company</p>
                                    <a href="{{ route('companies.index') }}" class="btn btn-success">Post a Job</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-4">
                        <a href="{{ route('login') }}" class="btn btn-outline-primary me-2">Login</a>
                        <a href="{{ route('register') }}" class="btn btn-outline-secondary">Register</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
