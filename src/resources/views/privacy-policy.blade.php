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
                    <div class="mb-5">
                        <h3 class="fs_30 grayColor1">Privacy Policy for Gain Solutions</h3>
                        <span>Effectve Date: 10 September 2023</span>
                    </div>

                    <div class="mb-5">
                        <p class="lead">1. Introduction</p>
                        <p>
                            Welcome to Gain Solution's Privacy Policy. At Gain Solution, we are committed to protecting your privacy and ensuring the security of your personal information. This Privacy Policy outlines how we collect, use, disclose, and safeguard your personal information. By using our software and visiting our website, you consent to the practices described in this policy.
                        </p>
                    </div>

                    <div class="mb-5">
                        <p class="lead">2. Definitions</p>
                        <div>
                            <p>- <strong>User</strong>: Any individual who uses our software or visits our website.</p>
                            <p>- <strong>Personal Information</strong>: Information that can be used to identify an individual, including but not limited to name, email address, location, and storage permissions. </p>
                            <p>- <strong>GDPR</strong>: General Data Protection Regulation, a European Union regulation designed to protect the privacy and personal data of EU residents. </p>
                        </div>
                    </div>

                    <div class="mb-5">
                        <p class="lead">3. Information We Collect</p>
                        <div>
                            <p>Gain Solution collects the following types of information:</p> 

                            <p>- <strong>User Location</strong>: We may collect your device's location to provide location-based services or improve your user experience.</p>
                            <p>- <strong>Storage Permission</strong>: Gain Solution may request access to your device's storage to store essential application data.</p>
                            <p>- <strong>Contact Information</strong>: When you contact us at support@gain.solutions, we collect and store your email address and any other information you provide to assist you with your inquiries.</p>
                            <p>- <strong>Website Visits</strong>: When you visit our website at https://gain.solutions, we collect anonymized data, such as your IP address, browser type, and pages visited, to improve our website's functionality and user experience. </p>
                        </div>
                    </div>

                    <div class="mb-5">
                        <p class="lead">4. How We Use Your Information</p>
                        <div>
                            <p>We use the information collected for the following purposes:</p>

                            <p>- <strong>To Provide Services</strong>: We use your location information and storage permissions to enhance the functionality of our software.</p>
                            <p>- <strong>User Support</strong>: Your contact information is used to respond to your inquiries, resolve issues, and provide customer support.</p>
                            <p>- <strong>Website Analytics</strong>: We analyze website visit data to improve our website's performance, content, and user experience.</p>
                        </div>
                    </div>

                    <div class="mb-5">
                        <p class="lead">5. GDPR and PIPEDA Compliance</p>
                        <div>
                            <p>Gain Solution is committed to GDPR and PIPEDA compliance. Here's how we ensure your data protection:</p>

                            <p>- <strong>Data Minimization</strong>: We collect only the data necessary for the purposes stated in this Privacy Policy.</p>
                            <p>- <strong>Consent</strong>: We obtain your explicit consent before collecting and processing your personal information.</p>
                            <p>- <strong>Security</strong>: We implement robust security measures to protect your data from unauthorized access or disclosure.</p>
                            <p>- <strong>Data Transfer</strong>: If we transfer your data internationally, we ensure adequate data protection safeguards.</p>
                            <p>- <strong>Access and Correction</strong>: You have the right to access, correct, or delete your personal information. Contact us at support@gain.solutions to exercise these rights.</p>
                        </div>
                    </div>

                    <div class="mb-5">
                        <p class="lead">6. Sharing Your Information</p>
                        <div>
                            <p>We do not sell, trade, or rent your personal information to third parties. However, we may share your information with:</p>

                            <p>- <strong>Service Providers</strong>: We may share your data with trusted service providers who assist us in providing and improving our services.</p>
                            <p>- <strong>Legal Compliance</strong>: We may disclose your information to comply with legal obligations, protect our rights, or respond to legal requests.</p>
                        </div>
                    </div>

                    <div class="mb-5">
                        <p class="lead">7. Data Retention</p>
                        <p> We retain your personal information only for as long as necessary to fulfill the purposes outlined in this Privacy Policy or as required by applicable laws. </p>
                    </div>

                    <div class="mb-5">
                        <p class="lead">8. Your choices</p>
                        <div>
                            <p>You have the following choices regarding your personal information:</p>
                            <p>- <strong>Access and Correction</strong>: You can access, correct, or delete your personal information by contacting us at support@gain.solutions.</p>
                            <p>- <strong>Marketing Communications</strong>: You can opt-out of receiving marketing communications from us by following the unsubscribe instructions in the emails we send.</p>
                        </div>
                    </div>

                    <div class="mb-5">
                        <p class="lead">9. Changes to this Privacy Policy</p>
                        <div>
                            <p>
                                We may update this Privacy Policy to reflect changes in our practices or for legal and regulatory reasons. Any changes will be posted on our website, and the date of the latest revision will be indicated.
                            </p>
                        </div>
                    </div>

                    <div class="mb-5">
                        <p class="lead">10. Contact Us</p>
                        <div>
                            <p> If you have any questions, concerns, or requests regarding this Privacy Policy or our data practices, please contact us at: </p>
                            <p>Gain Solution</p>
                        </div>
                    </div>

                    <div class="mb-5">
                        <p>Email: support@gain.solutions</p>
                        <p> Thank you for trusting Gain Solution with your personal information. We are committed to protecting your privacy and ensuring a secure and transparent data handling process. </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

</body>

</html>
