<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('{{ __('common.contact_us')  }}') }} - {{ config('app.name')  }}</title></head>
<body>
    <!-- Navigation -->
    <nav class="bg-white shadow-sm border-b border-gray-200 navbar-expand-lg bg-white shadow-sm -dark bg-primary-600">
        <div class="container mx-auto px-4 mx-auto">
            <a class="bg-white shadow-sm -brand" href="{{ route('front.home')  }}">
                <i class="fas fa-briefcase me-2"></i>
                {{ config('app.name')  }}
            </a>
            <button class="bg-white shadow-sm -toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="bg-white shadow-sm -toggler-icon"></span>
            </button>
            <div class="collapse bg-white shadow-sm -collapse" id="navbarNav">
                <ul class="bg-white shadow-sm -nav ms-auto">
                    <li class="nav-item">
                        <a class="text-gray-600 hover:text-gray-900 px-3 py-2 rounded-md text-sm font-medium" href="{{ route('front.home')  }}">{{ __('Home')  }}</a>
                    </li>
                    <li class="nav-item">
                        <a class="text-gray-600 hover:text-gray-900 px-3 py-2 rounded-md text-sm font-medium" href="{{ route('jobs.index')  }}">{{ __('Jobs')  }}</a>
                    </li>
                    <li class="nav-item">
                        <a class="text-gray-600 hover:text-gray-900 px-3 py-2 rounded-md text-sm font-medium" href="{{ route('companies.index')  }}">{{ __('Companies')  }}</a>
                    </li>
                    <li class="nav-item">
                        <a class="text-gray-600 hover:text-gray-900 px-3 py-2 rounded-md text-sm font-medium" href="{{ route('about-us')  }}">{{ __('{{ __('common.about_us')  }}') }}</a>
                    </li>
                    <li class="nav-item">
                        <a class="text-gray-600 hover:text-gray-900 px-3 py-2 rounded-md text-sm font-medium active" href="{{ route('contact')  }}">{{ __('Contact')  }}</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="bg-gray-100 py-5">
        <div class="container mx-auto px-4 mx-auto">
            <div class="flex flex-wrap">
                <div class="flex-1 -lg-12 text-center">
                    <h1 class="display-4 fw-bold text-primary-600">{{ __('{{ __('common.contact_us')  }}') }}</h1>
                    <p class="lead">{{ __('We\'d love to hear from you. Send us a message and we\'ll respond as soon as possible.')  }}</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Contact Form and Info Section -->
    <section class="py-5">
        <div class="container mx-auto px-4 mx-auto">
            @if (session('success'))
                <div class="px-4 py-3 rounded-md border border-gray-300 mb-4 bg-green-50 border border-gray-300 border-green-200 text-green-800 p-4 rounded-md mb-4 -dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i>
                    {{ session('success')  }}
                    <button type="button" class="px-4 py-2 rounded font-medium transition-colors -close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <div class="flex flex-wrap">
                <!-- Contact Form -->
                <div class="flex-1 -lg-8">
                    <div class="bg-white shadow rounded-lg overflow-hidden shadow-sm">
                        <div class="bg-white shadow rounded-lg overflow-hidden -header bg-primary-600 text-white">
                            <h4 class="mb-0">
                                <i class="fas fa-envelope me-2"></i>
                                {{ __('Send us a Message')  }}
                            </h4>
                        </div>
                        <div class="bg-white shadow rounded-lg overflow-hidden -body">
                            @if ($errors->any())
                                <div class="px-4 py-3 rounded-md border border-gray-300 mb-4 bg-red-50 border border-gray-300 border-red-200 text-red-800 p-4 rounded-md mb-4 -dismissible fade show" role="alert">
                                    <ul class="mb-0">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error  }}</li>
                                        @endforeach
                                    </ul>
                                    <button type="button" class="px-4 py-2 rounded font-medium transition-colors -close" data-bs-dismiss="alert"></button>
                                </div>
                            @endif

                            <form method="POST" action="{{ route('contact.submit')  }}">
                                @csrf
                                
                                <div class="flex flex-wrap">
                                    <div class="flex-1 -md-6">
                                        <div class="mb-3">
                                            <label for="first_name" class="block text-sm font-medium text-gray-700 mb-1">
                                                <i class="fas fa-user me-1"></i>
                                                {{ __('First Name')  }} <span class="text-red-600">*</span>
                                            </label>
                                            <input id="first_name" type="text" 
                                                   class="w-full px-3 py-2 border border-gray-300 border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500 @error("first_name') is-invalid @enderror" 
                                                   name="first_name" 
                                                   value="{{ old('first_name')  }}" 
                                                   required>
                                            @error('first_name')
                                                <div class="invalid-feedback">{{ $message  }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    
                                    <div class="flex-1 -md-6">
                                        <div class="mb-3">
                                            <label for="last_name" class="block text-sm font-medium text-gray-700 mb-1">
                                                <i class="fas fa-user me-1"></i>
                                                {{ __('Last Name')  }}
                                            </label>
                                            <input id="last_name" type="text" 
                                                   class="w-full px-3 py-2 border border-gray-300 border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500 @error("last_name') is-invalid @enderror" 
                                                   name="last_name" 
                                                   value="{{ old('last_name')  }}">
                                            @error('last_name')
                                                <div class="invalid-feedback">{{ $message  }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="flex flex-wrap">
                                    <div class="flex-1 -md-6">
                                        <div class="mb-3">
                                            <label for="email" class="block text-sm font-medium text-gray-700 mb-1">
                                                <i class="fas fa-envelope me-1"></i>
                                                {{ __('Email Address')  }} <span class="text-red-600">*</span>
                                            </label>
                                            <input id="email" type="email" 
                                                   class="w-full px-3 py-2 border border-gray-300 border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500 @error("email') is-invalid @enderror" 
                                                   name="email" 
                                                   value="{{ old('email')  }}" 
                                                   required>
                                            @error('email')
                                                <div class="invalid-feedback">{{ $message  }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    
                                    <div class="flex-1 -md-6">
                                        <div class="mb-3">
                                            <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">
                                                <i class="fas fa-phone me-1"></i>
                                                {{ __('Phone Number')  }}
                                            </label>
                                            <input id="phone" type="tel" 
                                                   class="w-full px-3 py-2 border border-gray-300 border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500 @error("phone') is-invalid @enderror" 
                                                   name="phone" 
                                                   value="{{ old('phone')  }}">
                                            @error('phone')
                                                <div class="invalid-feedback">{{ $message  }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="subject" class="block text-sm font-medium text-gray-700 mb-1">
                                        <i class="fas fa-tag me-1"></i>
                                        {{ __('{{ __('common.subject')  }}') }} <span class="text-red-600">*</span>
                                    </label>
                                    <input id="subject" type="text" 
                                           class="w-full px-3 py-2 border border-gray-300 border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500 @error("subject') is-invalid @enderror" 
                                           name="subject" 
                                           value="{{ old('subject')  }}" 
                                           required>
                                    @error('subject')
                                        <div class="invalid-feedback">{{ $message  }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="message" class="block text-sm font-medium text-gray-700 mb-1">
                                        <i class="fas fa-comment me-1"></i>
                                        {{ __('{{ __('common.message')  }}') }} <span class="text-red-600">*</span>
                                    </label>
                                    <textarea id="message" 
                                              class="w-full px-3 py-2 border border-gray-300 border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500 @error("message') is-invalid @enderror" 
                                              name="message" 
                                              rows="6" 
                                              required>{{ old('message')  }}</textarea>
                                    @error('message')
                                        <div class="invalid-feedback">{{ $message  }}</div>
                                    @enderror
                                    <div class="text-xs text-gray-500 mt-1">{{ __('Please provide as much detail as possible.')  }}</div>
                                </div>

                                <div class="d-grid">
                                    <button type="submit" class="inline-flex items-center px-4 py-2 border border-gray-300 border-transparent text-sm font-medium rounded-md transition duration-150 ease-in-out bg-primary-600 text-white hover: bg-primary-600 -700 px-4 py-2 rounded font-medium transition-colors -lg">
                                        <i class="fas fa-paper-plane me-1"></i>
                                        {{ __('{{ __('common.send_message')  }}') }}
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Contact Information -->
                <div class="flex-1 -lg-4">
                    <div class="flex flex-wrap">
                        <!-- Contact Details -->
                        <div class="flex-1 -12 mb-4">
                            <div class="bg-white shadow rounded-lg overflow-hidden h-full">
                                <div class="bg-white shadow rounded-lg overflow-hidden -body">
                                    <h5 class="bg-white shadow rounded-lg overflow-hidden -title">
                                        <i class="fas fa-info-circle text-primary-600 me-2"></i>
                                        {{ __('Contact Information')  }}
                                    </h5>
                                    
                                    <div class="mb-3">
                                        <div class="flex items-start">
                                            <i class="fas fa-map-marker-alt text-primary-600 me-3 mt-1"></i>
                                            <div>
                                                <h6 class="mb-1">{{ __('Address')  }}</h6>
                                                <p class="text-gray-500 mb-0">
                                                    123 Business Street<br>
                                                    Suite 100<br>
                                                    City, State 12345
                                                </p>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <div class="flex items-center">
                                            <i class="fas fa-phone text-primary-600 me-3"></i>
                                            <div>
                                                <h6 class="mb-1">{{ __('Phone')  }}</h6>
                                                <p class="text-gray-500 mb-0">+1 (555) 123-4567</p>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <div class="flex items-center">
                                            <i class="fas fa-envelope text-primary-600 me-3"></i>
                                            <div>
                                                <h6 class="mb-1">{{ __('Email')  }}</h6>
                                                <p class="text-gray-500 mb-0">contact@jobportal.com</p>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="mb-0">
                                        <div class="flex items-center">
                                            <i class="fas fa-clock text-primary-600 me-3"></i>
                                            <div>
                                                <h6 class="mb-1">{{ __('Business Hours')  }}</h6>
                                                <p class="text-gray-500 mb-0">
                                                    {{ __('Monday - Friday: 9:00 AM - 6:00 PM')  }}<br>
                                                    {{ __('Saturday: 10:00 AM - 4:00 PM')  }}<br>
                                                    {{ __('Sunday: Closed')  }}
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Quick Help -->
                        <div class="flex-1 -12 mb-4">
                            <div class="bg-white shadow rounded-lg overflow-hidden bg-gray-100">
                                <div class="bg-white shadow rounded-lg overflow-hidden -body">
                                    <h5 class="bg-white shadow rounded-lg overflow-hidden -title">
                                        <i class="fas fa-question-circle text-primary-600 me-2"></i>
                                        {{ __('Need Quick Help?')  }}
                                    </h5>
                                    <p class="bg-white shadow rounded-lg overflow-hidden -text">{{ __('Check out our frequently asked questions or browse our help center.')  }}</p>
                                    <div class="d-grid gap-2">
                                        <a href="#" class="inline-flex items-center px-4 py-2 border border-gray-300 border-transparent text-sm font-medium rounded-md transition duration-150 ease-in-out border-blue-600 text-blue-600 hover:bg-blue-600 hover:text-white px-4 py-2 rounded font-medium transition-colors -sm">
                                            <i class="fas fa-question me-1"></i>
                                            {{ __('FAQ')  }}
                                        </a>
                                        <a href="#" class="inline-flex items-center px-4 py-2 border border-gray-300 border-transparent text-sm font-medium rounded-md transition duration-150 ease-in-out btn-outline-info px-4 py-2 rounded font-medium transition-colors -sm">
                                            <i class="fas fa-life-ring me-1"></i>
                                            {{ __('Help Center')  }}
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Social Media -->
                        <div class="flex-1 -12">
                            <div class="bg-white shadow rounded-lg overflow-hidden">
                                <div class="bg-white shadow rounded-lg overflow-hidden -body text-center">
                                    <h5 class="bg-white shadow rounded-lg overflow-hidden -title">{{ __('Follow Us')  }}</h5>
                                    <div class="flex justify-center gap-3">
                                        <a href="#" class="inline-flex items-center px-4 py-2 border border-gray-300 border-transparent text-sm font-medium rounded-md transition duration-150 ease-in-out border-blue-600 text-blue-600 hover:bg-blue-600 hover:text-white px-4 py-2 rounded font-medium transition-colors -sm">
                                            <i class="fab fa-facebook"></i>
                                        </a>
                                        <a href="#" class="inline-flex items-center px-4 py-2 border border-gray-300 border-transparent text-sm font-medium rounded-md transition duration-150 ease-in-out btn-outline-info px-4 py-2 rounded font-medium transition-colors -sm">
                                            <i class="fab fa-twitter"></i>
                                        </a>
                                        <a href="#" class="inline-flex items-center px-4 py-2 border border-gray-300 border-transparent text-sm font-medium rounded-md transition duration-150 ease-in-out border-blue-600 text-blue-600 hover:bg-blue-600 hover:text-white px-4 py-2 rounded font-medium transition-colors -sm">
                                            <i class="fab fa-linkedin"></i>
                                        </a>
                                        <a href="#" class="inline-flex items-center px-4 py-2 border border-gray-300 border-transparent text-sm font-medium rounded-md transition duration-150 ease-in-out btn-outline-danger px-4 py-2 rounded font-medium transition-colors -sm">
                                            <i class="fab fa-instagram"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Map Section (Optional) -->
    <section class="py-5 bg-gray-100">
        <div class="container mx-auto px-4 mx-auto">
            <div class="flex flex-wrap">
                <div class="flex-1 -lg-12 text-center">
                    <h2 class="mb-4">{{ __('Find Us')  }}</h2>
                    <div class="embed-responsive embed-responsive-16by9">
                        <div class="bg-gray-600 flex items-center justify-center" style="height: 400px; border-radius: 10px;">
                            <div class="text-center text-white">
                                <i class="fas fa-map-marked-alt fa-3x mb-3"></i>
                                <h5>{{ __('Interactive Map')  }}</h5>
                                <p>{{ __('Map integration can be added here')  }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-800 text-white py-4">
        <div class="container mx-auto px-4 mx-auto">
            <div class="flex flex-wrap">
                <div class="flex-1 -md-6">
                    <h5>{{ config('app.name')  }}</h5>
                    <p>{{ __('Your trusted partner in career growth and talent acquisition.')  }}</p>
                </div>
                <div class="flex-1 -md-6 text-md-end">
                    <div class="social-links">
                        <a href="#" class="text-white me-3"><i class="fab fa-facebook"></i></a>
                        <a href="#" class="text-white me-3"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="text-white me-3"><i class="fab fa-linkedin"></i></a>
                        <a href="#" class="text-white"><i class="fab fa-instagram"></i></a>
                    </div>
                    <div class="mt-2">
                        <a href="{{ route('privacy.policy.list')  }}" class="text-white-50 me-3">{{ __('Privacy Policy')  }}</a>
                        <a href="{{ route('terms.conditions.list')  }}" class="text-white-50">{{ __('Terms of Service')  }}</a>
                    </div>
                </div>
            </div>
            <hr class="my-3">
            <div class="text-center">
                <p class="mb-0">&copy; {{ date('Y')  }} {{ config('app.name')  }}. {{ __('All rights reserved.')  }}</p>
            </div>
        </div>
    </footer></body>
</html> 