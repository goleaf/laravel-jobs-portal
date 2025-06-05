<div class="container mx-auto px-4 mx-auto px-5">
    <div class="flex flex-wrap">
        <div class="flex-1 lg-12">
            <div class="flex flex-wrap flex justify-center mb-5 mt-4">
                @if(Request::segment(1) =='admin')
                    <div class="flex-1 lg-6 mt-2">
                        <a class="inline-flex items-center px-4 py-2 border border-gray-300 border-transparent text-sm font-medium rounded-md transition duration-150 ease-in-out px-4 py-2 rounded font-medium transition-colors primary block admin-login">Super Admin Login</a></div>
                @elseif(Request::segment(2) =='candidate-login')
                    <div class="flex-1 lg-6 mt-2">
                        <a class="inline-flex items-center px-4 py-2 border border-gray-300 border-transparent text-sm font-medium rounded-md transition duration-150 ease-in-out px-4 py-2 rounded font-medium transition-colors primary block candidate-login">Candidate Login</a></div>
                @elseif(Request::segment(2) =='employee-login')
                    <div class="flex-1 lg-6 mt-2">
                        <a class="inline-flex items-center px-4 py-2 border border-gray-300 border-transparent text-sm font-medium rounded-md transition duration-150 ease-in-out px-4 py-2 rounded font-medium transition-colors primary block employee-login">Employee Login</a></div>
                @endif
                <div class="flex-1 lg-6 mt-2">
                    <a href="{{ url('/') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 border-transparent text-sm font-medium rounded-md transition duration-150 ease-in-out px-4 py-2 rounded font-medium transition-colors info block front-site">Front Site</a>                 
                </div>
            </div>
        </div>
    </div>
</div>
