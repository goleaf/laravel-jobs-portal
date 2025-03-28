@extends('front_web_template.layouts.app')
@section('title')
    Icon Components Documentation
@endsection

@section('content')
<div class="container py-5">
    <h1 class="mb-4">Icon Components Documentation</h1>
    <p class="lead mb-5">
        This documentation showcases all available icon components in the system. 
        Use these components to maintain consistency throughout the application.
    </p>

    <div class="row">
        <div class="col-lg-12 mb-4">
            <div class="card">
                <div class="card-header">
                    <h2 class="h5 mb-0">How to Use</h2>
                </div>
                <div class="card-body">
                    <p>Include icons in your Blade templates using the x-icons component syntax:</p>
                    <pre><code>&lt;x-icons.icon-name class="optional-classes" /&gt;</code></pre>
                    <p>All icons accept a <code>class</code> parameter for additional styling (default is <code>w-5 h-5</code>).</p>
                </div>
            </div>
        </div>
    </div>

    <h2 class="mb-4">Job Portal Icons</h2>
    
    <div class="row">
        <!-- Job Category Icons -->
        <div class="col-lg-12 mb-4">
            <div class="card">
                <div class="card-header">
                    <h3 class="h5 mb-0">Job Category Icons</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-sm-2 text-center mb-4">
                            <x-icons.briefcase class="w-10 h-10 mb-2" />
                            <p>briefcase</p>
                        </div>
                        <div class="col-sm-2 text-center mb-4">
                            <x-icons.factory class="w-10 h-10 mb-2" />
                            <p>factory</p>
                        </div>
                        <div class="col-sm-2 text-center mb-4">
                            <x-icons.office-building class="w-10 h-10 mb-2" />
                            <p>office-building</p>
                        </div>
                        <div class="col-sm-2 text-center mb-4">
                            <x-icons.remote-work class="w-10 h-10 mb-2" />
                            <p>remote-work</p>
                        </div>
                        <div class="col-sm-2 text-center mb-4">
                            <x-icons.building-storefront class="w-10 h-10 mb-2" />
                            <p>building-storefront</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Job Details Icons -->
        <div class="col-lg-12 mb-4">
            <div class="card">
                <div class="card-header">
                    <h3 class="h5 mb-0">Job Details Icons</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-sm-2 text-center mb-4">
                            <x-icons.location class="w-10 h-10 mb-2" />
                            <p>location</p>
                        </div>
                        <div class="col-sm-2 text-center mb-4">
                            <x-icons.clock class="w-10 h-10 mb-2" />
                            <p>clock</p>
                        </div>
                        <div class="col-sm-2 text-center mb-4">
                            <x-icons.calendar class="w-10 h-10 mb-2" />
                            <p>calendar</p>
                        </div>
                        <div class="col-sm-2 text-center mb-4">
                            <x-icons.calendar-days class="w-10 h-10 mb-2" />
                            <p>calendar-days</p>
                        </div>
                        <div class="col-sm-2 text-center mb-4">
                            <x-icons.money class="w-10 h-10 mb-2" />
                            <p>money</p>
                        </div>
                        <div class="col-sm-2 text-center mb-4">
                            <x-icons.experience class="w-10 h-10 mb-2" />
                            <p>experience</p>
                        </div>
                        <div class="col-sm-2 text-center mb-4">
                            <x-icons.graduation-cap class="w-10 h-10 mb-2" />
                            <p>graduation-cap</p>
                        </div>
                        <div class="col-sm-2 text-center mb-4">
                            <x-icons.academic-cap class="w-10 h-10 mb-2" />
                            <p>academic-cap</p>
                        </div>
                        <div class="col-sm-2 text-center mb-4">
                            <x-icons.tag class="w-10 h-10 mb-2" />
                            <p>tag</p>
                        </div>
                        <div class="col-sm-2 text-center mb-4">
                            <x-icons.chart class="w-10 h-10 mb-2" />
                            <p>chart</p>
                        </div>
                        <div class="col-sm-2 text-center mb-4">
                            <x-icons.puzzle class="w-10 h-10 mb-2" />
                            <p>puzzle</p>
                        </div>
                        <div class="col-sm-2 text-center mb-4">
                            <x-icons.time-contract class="w-10 h-10 mb-2" />
                            <p>time-contract</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Status Icons -->
        <div class="col-lg-12 mb-4">
            <div class="card">
                <div class="card-header">
                    <h3 class="h5 mb-0">Status Icons</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-sm-2 text-center mb-4">
                            <x-icons.check class="w-10 h-10 mb-2" />
                            <p>check</p>
                        </div>
                        <div class="col-sm-2 text-center mb-4">
                            <x-icons.checkmark-circle class="w-10 h-10 mb-2" />
                            <p>checkmark-circle</p>
                        </div>
                        <div class="col-sm-2 text-center mb-4">
                            <x-icons.clipboard-document-check class="w-10 h-10 mb-2" />
                            <p>clipboard-document-check</p>
                        </div>
                        <div class="col-sm-2 text-center mb-4">
                            <x-icons.status class="w-10 h-10 mb-2" />
                            <p>status</p>
                        </div>
                        <div class="col-sm-2 text-center mb-4">
                            <x-icons.shield-check class="w-10 h-10 mb-2" />
                            <p>shield-check</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Action Icons -->
        <div class="col-lg-12 mb-4">
            <div class="card">
                <div class="card-header">
                    <h3 class="h5 mb-0">Action Icons</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-sm-2 text-center mb-4">
                            <x-icons.heart class="w-10 h-10 mb-2" />
                            <p>heart</p>
                        </div>
                        <div class="col-sm-2 text-center mb-4">
                            <x-icons.share class="w-10 h-10 mb-2" />
                            <p>share</p>
                        </div>
                        <div class="col-sm-2 text-center mb-4">
                            <x-icons.eye class="w-10 h-10 mb-2" />
                            <p>eye</p>
                        </div>
                        <div class="col-sm-2 text-center mb-4">
                            <x-icons.chat class="w-10 h-10 mb-2" />
                            <p>chat</p>
                        </div>
                        <div class="col-sm-2 text-center mb-4">
                            <x-icons.download class="w-10 h-10 mb-2" />
                            <p>download</p>
                        </div>
                        <div class="col-sm-2 text-center mb-4">
                            <x-icons.document-duplicate class="w-10 h-10 mb-2" />
                            <p>document-duplicate</p>
                        </div>
                        <div class="col-sm-2 text-center mb-4">
                            <x-icons.refresh class="w-10 h-10 mb-2" />
                            <p>refresh</p>
                        </div>
                        <div class="col-sm-2 text-center mb-4">
                            <x-icons.mail class="w-10 h-10 mb-2" />
                            <p>mail</p>
                        </div>
                        <div class="col-sm-2 text-center mb-4">
                            <x-icons.pencil class="w-10 h-10 mb-2" />
                            <p>pencil</p>
                        </div>
                        <div class="col-sm-2 text-center mb-4">
                            <x-icons.archive class="w-10 h-10 mb-2" />
                            <p>archive</p>
                        </div>
                        <div class="col-sm-2 text-center mb-4">
                            <x-icons.inbox class="w-10 h-10 mb-2" />
                            <p>inbox</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Interface Icons -->
        <div class="col-lg-12 mb-4">
            <div class="card">
                <div class="card-header">
                    <h3 class="h5 mb-0">Interface Icons</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-sm-2 text-center mb-4">
                            <x-icons.filter class="w-10 h-10 mb-2" />
                            <p>filter</p>
                        </div>
                        <div class="col-sm-2 text-center mb-4">
                            <x-icons.search class="w-10 h-10 mb-2" />
                            <p>search</p>
                        </div>
                        <div class="col-sm-2 text-center mb-4">
                            <x-icons.menu-alt-1 class="w-10 h-10 mb-2" />
                            <p>menu-alt-1</p>
                        </div>
                        <div class="col-sm-2 text-center mb-4">
                            <x-icons.user class="w-10 h-10 mb-2" />
                            <p>user</p>
                        </div>
                        <div class="col-sm-2 text-center mb-4">
                            <x-icons.user-group class="w-10 h-10 mb-2" />
                            <p>user-group</p>
                        </div>
                        <div class="col-sm-2 text-center mb-4">
                            <x-icons.cog class="w-10 h-10 mb-2" />
                            <p>cog</p>
                        </div>
                        <div class="col-sm-2 text-center mb-4">
                            <x-icons.notification class="w-10 h-10 mb-2" />
                            <p>notification</p>
                        </div>
                        <div class="col-sm-2 text-center mb-4">
                            <x-icons.question-mark-circle class="w-10 h-10 mb-2" />
                            <p>question-mark-circle</p>
                        </div>
                        <div class="col-sm-2 text-center mb-4">
                            <x-icons.lock-closed class="w-10 h-10 mb-2" />
                            <p>lock-closed</p>
                        </div>
                        <div class="col-sm-2 text-center mb-4">
                            <x-icons.globe class="w-10 h-10 mb-2" />
                            <p>globe</p>
                        </div>
                        <div class="col-sm-2 text-center mb-4">
                            <x-icons.x-mark class="w-10 h-10 mb-2" />
                            <p>x-mark</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Communication Icons -->
        <div class="col-lg-12 mb-4">
            <div class="card">
                <div class="card-header">
                    <h3 class="h5 mb-0">Communication Icons</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-sm-2 text-center mb-4">
                            <x-icons.camera class="w-10 h-10 mb-2" />
                            <p>camera</p>
                        </div>
                        <div class="col-sm-2 text-center mb-4">
                            <x-icons.video-camera class="w-10 h-10 mb-2" />
                            <p>video-camera</p>
                        </div>
                        <div class="col-sm-2 text-center mb-4">
                            <x-icons.phone class="w-10 h-10 mb-2" />
                            <p>phone</p>
                        </div>
                        <div class="col-sm-2 text-center mb-4">
                            <x-icons.handshake class="w-10 h-10 mb-2" />
                            <p>handshake</p>
                        </div>
                        <div class="col-sm-2 text-center mb-4">
                            <x-icons.language class="w-10 h-10 mb-2" />
                            <p>language</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 