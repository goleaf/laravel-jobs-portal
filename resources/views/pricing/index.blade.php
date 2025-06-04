@extends('employer.layouts.app')
@section('title')
    {{ __('messages.employer_menu.manage_subscriptions')  }}
@endsection
@section('content')
    @include('flash::message')
    <div class="flex flex-wrap justify-center">
        @foreach($plans as $plan)
            <div class="col-xl-4 flex-1 -md-6 flex align-items-stretch">
                <div class="bg-white rounded-lg shadow-md border border-gray-300 border-gray-200 pricing- bg-white shadow rounded-lg overflow-hidden text-center flex-fill">
                    <div class="bg-white shadow rounded-lg overflow-hidden -body px-xl-10 px-7 py-xl-14 py-10 flex flex-col">
                        <h3 class="text-gray-900 fs-2">{{ html_entity_decode($plan->name)  }}</h3>
                        <div class="flex justify-center mt-5">
                            <h4 class="text-center mb-6 mt-2">
                                <span class="fa-3x fw-bolder">{{ empty($plan->salaryCurrency->currency_icon)?'$':$plan->salaryCurrency->currency_icon  }}{{ $plan->amount  }}</span>
                                <span class="h6 text-gray-800 ml-2">{{ __('messages.plan.per_month')  }}</span>
                            </h4>
                        </div>
                        @if(isset($subscription) && $subscription->plan_id == $plan->id)
                            @if($subscription->stripe_status != 'trialing')
                                <div class="py-4 fs-2 text-center">
                                    @if(isset($subscription->ends_at))
                                        {{ __('messages.plan.ends_at').': '.\Carbon\Carbon::parse($subscription->ends_at)->translatedFormat('jS M,Y')  }}
                                    @else
                                        {{ __('messages.plan.renews_on').': '.\Carbon\Carbon::parse($subscription->current_period_end)->translatedFormat('jS M,Y')  }}
                                    @endif
                                </div>
                            @endif
                        @endif
                        <ul class="pricing-plan-features text-gray-600 fs-4 mb-9">
                            <li>
                                <i class="fa-solid fa-check me-3"></i>{{ $plan->allowed_jobs.' '.($plan->allowed_jobs > 1 ? __('messages.plan.jobs_allowed') : __('messages.plan.job_allowed'))  }}
                            </li>
                            <li>@if($plan->is_trial_plan)
                                    <i class="fa-solid fa-check me-3"></i>
                                @else
                                    <i class="fas fa-times text-red-600 me-3"></i>
                                @endif
                                {{ __('messages.plan.is_trial_plan')  }}</li>
                            <li>@if(isset($subscription) && $subscription->plan_id == $plan->id)
                                    <i class="fas fa-briefcase text-primary-600 me-3"></i>
                                    {{ $jobsCount.' '.($jobsCount > 1 ? __('messages.plan.jobs_used') : __('messages.plan.job_used')) }}
                                @endif</li>
                        </ul>
                        {{ --                        @dd($subscription->plan_id == $plan->id)-- }}
                        @if(isset($subscription) && $subscription->plan_id == $plan->id)
                            <div class="flex justify-center">
                                @if($subscription->current_period_end <= date('Y-m-d H:i:s'))
                                    <a href="{{ route('payment-method-screen', $plan->id)  }}"
                                       data-id="{{ $plan->id  }}"
                                       class="inline-flex items-center px-4 py-2 border border-gray-300 border-transparent text-sm font-medium rounded-md transition duration-150 ease-in-out bg-yellow-500 text-white hover:bg-yellow-600 pricing-plan- px-4 py-2 rounded font-medium transition-colors mt-auto align-self-center">
                                        {{ __('messages.plan.upgrade')  }}
                                    </a>
                                @else
                                    <a href="javascript:void(0)"
                                       class="inline-flex items-center px-4 py-2 border border-gray-300 border-transparent text-sm font-medium rounded-md transition duration-150 ease-in-out bg-green-600 text-white hover:bg-green-700 pricing-plan- px-4 py-2 rounded font-medium transition-colors mt-auto align-self-center">{{ __('messages.plan.current_plan')  }} </a>
                                @endif
                                @if($subscription->ststripe_statu != 'trialing')
                                    @if(isset($subscription->ends_at))
                                        <a href="javascript:void(0)"
                                           class="inline-flex items-center px-4 py-2 border border-gray-300 border-transparent text-sm font-medium rounded-md transition duration-150 ease-in-out bg-red-600 text-white hover:bg-red-700 pricing-plan- px-4 py-2 rounded font-medium transition-colors mt-auto align-self-center subscription ms-3">{{ __('messages.plan.subscription_cancelled')  }}</a>
                                    @else
                                        @if($subscription['name'] != 'Trial Plan')
                                            <a href="javascript:void(0)"
                                               class="inline-flex items-center px-4 py-2 border border-gray-300 border-transparent text-sm font-medium rounded-md transition duration-150 ease-in-out bg-red-600 text-white hover:bg-red-700 pricing-plan- px-4 py-2 rounded font-medium transition-colors mt-auto align-self-center cancel-subscription ms-3">{{ __('messages.plan.cancel_subscription')  }}</a>
                                        @endif
                                    @endif
                                @endif
                            </div>
                        @else
                            @if($plan->is_trial_plan)
                                <a href="javascript:void(0)" data-id="{{ $plan->id  }}"
                                   class="inline-flex items-center px-4 py-2 border border-gray-300 border-transparent text-sm font-medium rounded-md transition duration-150 ease-in-out bg-blue-500 text-white hover:bg-blue-600 pricing-plan- px-4 py-2 rounded font-medium transition-colors mt-auto align-self-center"
                                   style="pointer-events: none;">{{ __('messages.plan.is_trial_plan')  }} </a>
                            @elseif(!empty(processingPlan($plan->id)) )
                                <a
                                        data-id="{{ $plan->id  }}"
                                        class="inline-flex items-center px-4 py-2 border border-gray-300 border-transparent text-sm font-medium rounded-md transition duration-150 ease-in-out bg-gray-500 text-white hover:bg-gray-600 pricing-plan- px-4 py-2 rounded font-medium transition-colors mt-auto align-self-center cursor-default"> {{ __('messages.plan.processing')  }}</a>
                            @else
                                @if($activePlanId !== $plan->id)
                                    <a href="{{ route('payment-method-screen', $plan->id)  }}"
                                       data-id="{{ $plan->id  }}"
                                       class="inline-flex items-center px-4 py-2 border border-gray-300 border-transparent text-sm font-medium rounded-md transition duration-150 ease-in-out bg-blue-500 text-white hover:bg-blue-600 pricing-plan- px-4 py-2 rounded font-medium transition-colors mt-auto align-self-center"> {{ __('messages.plan.purchase')  }}</a>
                                @endif
                            @endif
                        @endif
                    </div>
                </div>
            </div>
        @endforeach
        @include('pricing.cancel_subscription_modal')
    </div>
    {{ Form::hidden('subscribe-text',__('messages.plan.purchase'), ['id' => 'subscribeText']) }}
@endsection
