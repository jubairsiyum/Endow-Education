@extends('layouts.student')

@section('page-title', 'Frequently Asked Questions')
@section('breadcrumb', 'Home / FAQ')

@section('content')
    <div class="row">
        <div class="col-lg-10 mx-auto">
            <div class="card-custom mb-4">
                <div class="card-body-custom text-center py-5">
                    <i class="fas fa-question-circle fa-4x text-danger mb-3"></i>
                    <h3 class="fw-bold mb-2">How Can We Help You?</h3>
                    <p class="text-muted mb-4">Find answers to commonly asked questions about the application process, documents, and more.</p>
                    
                    <!-- Search Box -->
                    <div class="col-md-6 mx-auto">
                        <div class="input-group">
                            <span class="input-group-text bg-white">
                                <i class="fas fa-search text-muted"></i>
                            </span>
                            <input type="text" 
                                   class="form-control" 
                                   id="faqSearch" 
                                   placeholder="Search FAQ...">
                        </div>
                    </div>
                </div>
            </div>

            <!-- FAQ Categories -->
            <div class="row g-4 mb-4">
                <div class="col-md-4">
                    <div class="card-custom text-center h-100 faq-category-card" data-category="application">
                        <div class="card-body-custom py-4">
                            <div class="bg-danger bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" 
                                 style="width: 70px; height: 70px;">
                                <i class="fas fa-file-alt fa-2x text-danger"></i>
                            </div>
                            <h5 class="fw-bold">Application Process</h5>
                            <p class="text-muted small mb-0">Learn about applying and requirements</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card-custom text-center h-100 faq-category-card" data-category="documents">
                        <div class="card-body-custom py-4">
                            <div class="bg-primary bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" 
                                 style="width: 70px; height: 70px;">
                                <i class="fas fa-folder-open fa-2x text-primary"></i>
                            </div>
                            <h5 class="fw-bold">Document Submission</h5>
                            <p class="text-muted small mb-0">Guidelines for uploading documents</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card-custom text-center h-100 faq-category-card" data-category="general">
                        <div class="card-body-custom py-4">
                            <div class="bg-success bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" 
                                 style="width: 70px; height: 70px;">
                                <i class="fas fa-info-circle fa-2x text-success"></i>
                            </div>
                            <h5 class="fw-bold">General Questions</h5>
                            <p class="text-muted small mb-0">Other commonly asked questions</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Application Process FAQs -->
            <div class="card-custom mb-4" data-category="application">
                <div class="card-header-custom">
                    <h5 class="mb-0"><i class="fas fa-file-alt text-danger me-2"></i>Application Process</h5>
                </div>
                <div class="card-body-custom p-0">
                    <div class="accordion" id="applicationAccordion">
                        <div class="accordion-item border-0 border-bottom">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#app1">
                                    How do I start my application?
                                </button>
                            </h2>
                            <div id="app1" class="accordion-collapse collapse" data-bs-parent="#applicationAccordion">
                                <div class="accordion-body">
                                    To start your application, first ensure your account is approved. Once approved, complete your profile information, and your counselor will assign you a target university and program. You can then begin submitting the required documents from the "Submit Documents" page.
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item border-0 border-bottom">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#app2">
                                    How long does the application process take?
                                </button>
                            </h2>
                            <div id="app2" class="accordion-collapse collapse" data-bs-parent="#applicationAccordion">
                                <div class="accordion-body">
                                    The application timeline varies depending on the university and program. Typically, document review takes 5-7 business days. Complete applications are processed within 2-4 weeks. We recommend submitting all documents at least 2-3 months before your intended start date.
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item border-0 border-bottom">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#app3">
                                    Can I change my target university or program?
                                </button>
                            </h2>
                            <div id="app3" class="accordion-collapse collapse" data-bs-parent="#applicationAccordion">
                                <div class="accordion-body">
                                    Yes, you can request a change to your target university or program. Please contact your assigned counselor or use the Emergency Contact page to reach out to support. Changes may require additional documents or affect your application timeline.
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item border-0">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#app4">
                                    What happens after I submit my application?
                                </button>
                            </h2>
                            <div id="app4" class="accordion-collapse collapse" data-bs-parent="#applicationAccordion">
                                <div class="accordion-body">
                                    After submission, your counselor will review all documents. You'll receive notifications via email for any issues or approvals. Once all documents are approved, your application will be forwarded to the university. You can track your progress from the Dashboard.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Document Submission FAQs -->
            <div class="card-custom mb-4" data-category="documents">
                <div class="card-header-custom">
                    <h5 class="mb-0"><i class="fas fa-folder-open text-primary me-2"></i>Document Submission</h5>
                </div>
                <div class="card-body-custom p-0">
                    <div class="accordion" id="documentsAccordion">
                        <div class="accordion-item border-0 border-bottom">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#doc1">
                                    What file formats are accepted?
                                </button>
                            </h2>
                            <div id="doc1" class="accordion-collapse collapse" data-bs-parent="#documentsAccordion">
                                <div class="accordion-body">
                                    We accept PDF, JPG, and PNG formats. PDF is strongly recommended for text documents. Each file must be under 5MB in size. Ensure documents are clear, legible, and properly oriented before uploading.
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item border-0 border-bottom">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#doc2">
                                    What if my document is rejected?
                                </button>
                            </h2>
                            <div id="doc2" class="accordion-collapse collapse" data-bs-parent="#documentsAccordion">
                                <div class="accordion-body">
                                    If a document is rejected, you'll see a "Needs Revision" status with specific feedback from your counselor. Read the feedback carefully, make the necessary corrections, and re-upload the document. You can upload a new version directly from the Submit Documents page.
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item border-0 border-bottom">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#doc3">
                                    Do I need to submit documents in a specific order?
                                </button>
                            </h2>
                            <div id="doc3" class="accordion-collapse collapse" data-bs-parent="#documentsAccordion">
                                <div class="accordion-body">
                                    Yes, documents are listed in the recommended submission order on the Submit Documents page. While you can upload documents in any sequence, following the suggested order helps streamline the review process and may result in faster approval.
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item border-0 border-bottom">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#doc4">
                                    Can I delete or replace a submitted document?
                                </button>
                            </h2>
                            <div id="doc4" class="accordion-collapse collapse" data-bs-parent="#documentsAccordion">
                                <div class="accordion-body">
                                    You can replace documents that are in "Pending" or "Needs Revision" status. Once a document is approved, you cannot delete it. If you need to update an approved document, please contact your counselor for assistance.
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item border-0">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#doc5">
                                    How will I know when my documents are reviewed?
                                </button>
                            </h2>
                            <div id="doc5" class="accordion-collapse collapse" data-bs-parent="#documentsAccordion">
                                <div class="accordion-body">
                                    You'll receive email notifications when documents are reviewed. Additionally, you can check the status anytime on the Submit Documents page. Document statuses include: Not Submitted, Under Review, Approved, and Needs Revision.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- General FAQs -->
            <div class="card-custom mb-4" data-category="general">
                <div class="card-header-custom">
                    <h5 class="mb-0"><i class="fas fa-info-circle text-success me-2"></i>General Questions</h5>
                </div>
                <div class="card-body-custom p-0">
                    <div class="accordion" id="generalAccordion">
                        <div class="accordion-item border-0 border-bottom">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#gen1">
                                    How do I contact my counselor?
                                </button>
                            </h2>
                            <div id="gen1" class="accordion-collapse collapse" data-bs-parent="#generalAccordion">
                                <div class="accordion-body">
                                    Your assigned counselor's contact information is displayed on your Dashboard. You can also visit the Emergency Contact page for additional contact options. For urgent matters, use the emergency hotline provided on the Emergency Contact page.
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item border-0 border-bottom">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#gen2">
                                    How do I reset my password?
                                </button>
                            </h2>
                            <div id="gen2" class="accordion-collapse collapse" data-bs-parent="#generalAccordion">
                                <div class="accordion-body">
                                    Click the "Forgot Password" link on the login page. Enter your registered email address, and you'll receive a password reset link. Follow the instructions in the email to set a new password. If you don't receive the email, check your spam folder.
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item border-0 border-bottom">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#gen3">
                                    Can I track my application status?
                                </button>
                            </h2>
                            <div id="gen3" class="accordion-collapse collapse" data-bs-parent="#generalAccordion">
                                <div class="accordion-body">
                                    Yes! Your Dashboard provides a complete overview of your application status, including document submission progress, pending tasks, and overall completion percentage. The Dashboard is updated in real-time as your documents are reviewed.
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item border-0 border-bottom">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#gen4">
                                    Is my personal information secure?
                                </button>
                            </h2>
                            <div id="gen4" class="accordion-collapse collapse" data-bs-parent="#generalAccordion">
                                <div class="accordion-body">
                                    Yes, we take data security seriously. All personal information and documents are encrypted and stored securely. Your data is only accessible to authorized personnel involved in processing your application. We comply with all relevant data protection regulations.
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item border-0">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#gen5">
                                    What are the office hours?
                                </button>
                            </h2>
                            <div id="gen5" class="accordion-collapse collapse" data-bs-parent="#generalAccordion">
                                <div class="accordion-body">
                                    Our office hours are Monday to Friday, 9:00 AM to 6:00 PM (local time). For emergency situations outside office hours, please use the emergency hotline available on the Emergency Contact page. We respond to all inquiries within 24-48 hours.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Still Have Questions -->
            <div class="card-custom border-0 bg-danger bg-opacity-10">
                <div class="card-body-custom text-center py-5">
                    <i class="fas fa-headset fa-3x text-danger mb-3"></i>
                    <h4 class="fw-bold mb-2">Still Have Questions?</h4>
                    <p class="text-muted mb-4">Can't find what you're looking for? Our support team is here to help!</p>
                    <a href="{{ route('student.emergency-contact') }}" class="btn btn-primary-custom">
                        <i class="fas fa-phone-alt me-2"></i>Contact Support
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
<style>
    .faq-category-card {
        cursor: pointer;
        transition: all 0.3s ease;
        border: 2px solid transparent;
    }
    .faq-category-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        border-color: #DC143C;
    }
    .accordion-button:not(.collapsed) {
        background-color: #DC143C;
        color: white;
    }
    .accordion-button:focus {
        box-shadow: none;
        border-color: rgba(220, 20, 60, 0.25);
    }
</style>
@endpush

@push('scripts')
<script>
    // FAQ Search functionality
    document.getElementById('faqSearch').addEventListener('input', function(e) {
        const searchTerm = e.target.value.toLowerCase();
        const accordions = document.querySelectorAll('.accordion-item');
        
        accordions.forEach(item => {
            const text = item.textContent.toLowerCase();
            if (text.includes(searchTerm)) {
                item.style.display = '';
            } else {
                item.style.display = 'none';
            }
        });

        // Show/hide category sections
        document.querySelectorAll('[data-category]').forEach(section => {
            const visibleItems = section.querySelectorAll('.accordion-item:not([style*="display: none"])');
            if (visibleItems.length === 0 && section.classList.contains('card-custom')) {
                section.style.display = 'none';
            } else {
                section.style.display = '';
            }
        });
    });

    // Category filter (optional enhancement)
    document.querySelectorAll('.faq-category-card').forEach(card => {
        card.addEventListener('click', function() {
            const category = this.dataset.category;
            document.querySelectorAll('[data-category]').forEach(section => {
                if (section.classList.contains('card-custom')) {
                    section.style.display = section.dataset.category === category ? '' : 'none';
                }
            });
            
            // Scroll to first visible FAQ section
            setTimeout(() => {
                const firstVisible = document.querySelector('.card-custom[data-category]:not([style*="display: none"])');
                if (firstVisible) {
                    firstVisible.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }
            }, 100);
        });
    });
</script>
@endpush
