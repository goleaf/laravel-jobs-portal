@extends('layouts.app')

@section('title', 'About Us')

@section('content')
<div class="container mx-auto px-6 py-12">
    <div class="max-w-4xl mx-auto">
        <h1 class="text-4xl font-bold text-gray-900 dark:text-white mb-8 text-center">About Us</h1>
        
        <div class="prose prose-lg max-w-none dark:prose-invert">
            <p class="text-xl text-gray-600 dark:text-gray-300 mb-8 text-center">
                Welcome to our Job Portal - connecting talented professionals with great opportunities.
            </p>
            
            <div class="grid md:grid-cols-2 gap-8 mb-12">
                <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-md">
                    <h2 class="text-2xl font-semibold text-gray-900 dark:text-white mb-4">Our Mission</h2>
                    <p class="text-gray-600 dark:text-gray-300">
                        To bridge the gap between exceptional talent and outstanding career opportunities, 
                        creating a platform where both job seekers and employers can find their perfect match.
                    </p>
                </div>
                
                <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-md">
                    <h2 class="text-2xl font-semibold text-gray-900 dark:text-white mb-4">Our Vision</h2>
                    <p class="text-gray-600 dark:text-gray-300">
                        To become the leading job portal platform, empowering careers and businesses 
                        through innovative technology and meaningful connections.
                    </p>
                </div>
            </div>
            
            <div class="bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-gray-800 dark:to-gray-700 p-8 rounded-lg">
                <h2 class="text-2xl font-semibold text-gray-900 dark:text-white mb-4">Why Choose Our Platform?</h2>
                <ul class="grid md:grid-cols-2 gap-4 text-gray-600 dark:text-gray-300">
                    <li class="flex items-center">
                        <svg class="w-5 h-5 text-green-500 mr-3" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"/>
                        </svg>
                        Extensive job listings
                    </li>
                    <li class="flex items-center">
                        <svg class="w-5 h-5 text-green-500 mr-3" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"/>
                        </svg>
                        Advanced search filters
                    </li>
                    <li class="flex items-center">
                        <svg class="w-5 h-5 text-green-500 mr-3" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"/>
                        </svg>
                        Professional networking
                    </li>
                    <li class="flex items-center">
                        <svg class="w-5 h-5 text-green-500 mr-3" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"/>
                        </svg>
                        Secure platform
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection 