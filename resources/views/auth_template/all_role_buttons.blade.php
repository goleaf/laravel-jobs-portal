<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mx-auto px-4 mx-auto px-4 mx-auto px-4 mx-auto px-5">
    <div class="flex flex-wrap">
        <div class="flex-1 lg-12">
            <div class="flex flex-wrap flex justify-center mb-5 mt-4">
                @if(Request::segment(1) =='admin')
                    <div class="flex-1 lg-6 mt-2">
                        <a class="border border-gray-300 bg-transparent">Super Admin Login</a></div>
                @elseif(Request::segment(2) =='candidate-login')
                    <div class="flex-1 lg-6 mt-2">
                        <a class="border border-gray-300 bg-transparent">Candidate Login</a></div>
                @elseif(Request::segment(2) =='employee-login')
                    <div class="flex-1 lg-6 mt-2">
                        <a class="border border-gray-300 bg-transparent">Employee Login</a></div>
                @endif
                <div class="flex-1 lg-6 mt-2">
                    <a href="{{ url('/') }}" class="border border-gray-300 bg-transparent">Front Site</a>                 
                </div>
            </div>
        </div>
    </div>
</div>
