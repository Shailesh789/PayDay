@php
    $logovalue = @\App\Http\Composer\Helper\LogoIcon::new(true)->logoIcon();
    ['logo' => $logo] = @$logovalue ?? '';
@endphp
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge" />
    {{--<meta name="csrf-token" content="{{ csrf_token() }}">--}}
    <link rel="shortcut icon" href="{{ url(settings('tenant_icon', 'app_icon')) }}" />
    <link rel="apple-touch-icon" href="{{ url(settings('tenant_icon', 'app_icon')) }}" />
    <link rel="apple-touch-icon-precomposed" href="{{ url(settings('tenant_icon', 'app_icon')) }}" />

    <title>@yield('title') - {{ settings('tenant_name', 'app_name') }}</title>
    @stack('before-styles')
    {{ style(mix('css/core.css')) }}
    {{ style(mix('css/fontawesome.css')) }}
    {{ style(mix('css/dropzone.css')) }}
    {{ style('vendor/summernote/summernote-bs4.css') }}
    @stack('after-styles')
</head>

<body>
    <div class="new-main-content">
        <div class="container">
            <div class="row py-5 mt-5">
                <div class="col-md-12">

                    <img class="mb-4" width="200" src="{{$logo}} " alt="">
                    <h3 class="mb-5">Terms and Conditions</h3>

                    <div class="mb-5">
                        <p class="lead">1. Acceptance of Terms</p>
                        <p>By accessing or using the software “Payday” and the services provided by Gain Solution Ltd, you agree to comply with and be bound by these Terms and Conditions (“T & C”). If you do not agree with these terms, please do not use the software or services.</p>
                    </div>

                    <div class="mb-5">
                        <p class="lead">2. Compliance with Laws</p>
                        <p>You agree to use the Payday software and services in accordance with all applicable international, federal, state, and local laws and regulations. You shall not use the software in any way that violates these laws or regulations.</p>
                    </div>

                    <div class="mb-5">
                        <p class="lead">3. Copyright and Trademarks</p>
                        <p>The Payday software and all related information are the property of Gain Solution Ltd or its licensors and are protected by copyright, trademark, and other intellectual property laws. Users are prohibited from modifying, copying, distributing, transmitting, displaying, publishing, selling, licensing, creating derivative works, or using any content or information from Payday for commercial or public purposes without written permission.</p>
                    </div>

                    <div class="mb-5">
                        <p class="lead">4. Prohibited Actions</p>
                        <p>Users agree not to tamper with, modify, move, add to, delete, manipulate, or disrupt the Payday software or the information contained within it. Reverse engineering, disassembling, or any unlawful use of the software is strictly prohibited.</p>
                    </div>

                    <div class="mb-5">
                        <p class="lead">5. Third Party Information</p>
                        <p>While Gain Solution Ltd monitors the information within Payday, some information may be supplied by independent third parties. Although efforts are made to ensure accuracy, Gain Solution Ltd makes no warranty as to the accuracy of such information.</p>
                    </div>

                    <div class="mb-5">
                        <p class="lead">6. Links to Third Party Sites</p>
                        <p>Payday may contain links to other websites not under the control of Gain Solution Ltd. These links are provided for convenience, and Gain Solution Ltd does not endorse or accept responsibility for the content of these external websites.</p>
                    </div>

                    <div class="mb-5">
                        <p class="lead">7. No Warranties</p>
                        <p>Payday and its associated documents are provided “as is” without any warranty, whether express or implied, including but not limited to merchantability, fitness for a particular purpose, and non-infringement. Gain Solution Ltd does not guarantee the accuracy or completeness of the information provided.</p>
                    </div>

                    <div class="mb-5">
                        <p class="lead">8. Privacy</p>
                        <p>Gain Solution Ltd values user privacy and follows a strict Privacy Policy to protect user information. For more details, please refer to our Privacy Policy.</p>
                    </div>

                    <div class="mb-5">
                        <p class="lead">9. Security</p>
                        <p>While we employ security measures to protect user data, the security of information transmitted over the internet cannot be guaranteed. Users are responsible for maintaining the security of their login credentials and should not disclose them to unauthorized third parties.</p>
                    </div>

                    <div class="mb-5">
                        <p class="lead">10. Transmission of Personal Data</p>
                        <p>By providing personal information through Payday, you consent to the transmission of such information over international borders for processing in accordance with Gain Solution Ltd’s standard business practices and Privacy Policy.</p>
                    </div>

                    <div class="mb-5">
                        <p class="lead">11. Access to Password Protected/Secure Areas</p>
                        <p>Access to password-protected or secure areas of Payday is restricted to authorized users only. Unauthorized access is prohibited and may lead to legal action.</p>
                    </div>

                    <div class="mb-5">
                        <p class="lead">12. Fees and Payment</p>
                        <p>All fees associated with Payday are detailed in the applicable Service Order Form (SOF) and are due within the specified timeframe. Late payments may result in suspension of access. Payment disputes should be resolved promptly.</p>
                    </div>

                    <div class="mb-5">
                        <p class="lead">13. Term, Termination, and Suspension</p>
                        <p>The term of this agreement, renewal conditions, and grounds for suspension or termination are outlined in the SOF.</p>
                    </div>

                    <div class="mb-5">
                        <p class="lead">14. Warranties and Disclaimer</p>
                        <p>Gain Solution Ltd provides warranties regarding the performance of the Payday software and protection against malware. However, other warranties, whether express or implied, are disclaimed.</p>
                    </div>

                    <div class="mb-5">
                        <p class="lead">15. Indemnification</p>
                        <p>Customers are required to defend and indemnify Gain Solution Ltd against third-party claims resulting from their actions, including unauthorized handling of customer data.</p>
                    </div>

                    <div class="mb-5">
                        <p class="lead">16. Miscellaneous</p>
                        <ul>
                            <li>Use of third parties for payment processing is permitted.</li>
                            <li>Assignment of rights or obligations is subject to prior written consent.</li>
                            <li>This agreement supersedes prior agreements and contains the entire understanding between parties.</li>
                            <li>Gain Solution Ltd may identify Customer as a customer in promotional materials unless otherwise requested.</li>
                            <li>The parties maintain an independent contractor relationship.</li>
                            <li>Both parties affirm their commitment to anti-corruption measures.</li>
                            <li>The governing law for this agreement is the law of the State of California.</li>
                            <li>Disputes arising from this agreement will be subject to arbitration in San Francisco, California.</li>
                        </ul>
                    </div>

                    <div class="mb-5">
                        <p class="lead">17. Definitions</p>
                        <p>Various terms, including “Account,” “Affiliate,” “Confidential Information,” “Customer Data,” “Documentation,” “End User,” “Mobile App,” “Personal Data,” “Services,” “Service Plans,” “Software,” “Update,” “User” or “Agent,” are defined for clarity within the agreement.</p>
                    </div>

                    <p class="lead">18. Copyright Notice</p>
                    <p>© Copyright 2023 Gain Solution Ltd. All rights reserved.</p>

                </div>
            </div>
        </div>
    </div>

</body>

</html>
