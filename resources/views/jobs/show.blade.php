@extends('layouts.simple')

@section('title', 'Job Details')

@section('content')
<div class="container mx-auto py-5">
    <div class="flex flex-wrap">
        <div class="flex-1 -lg-8">
            <!-- Job Header -->
            <div class="bg-white shadow rounded-lg overflow-hidden shadow-sm mb-4">
                <div class="bg-white shadow rounded-lg overflow-hidden -body p-4">
                    <div class="flex align-items-start">
                        <div class="company-logo me-4">
                            <i class="fas fa-building fa-4x text-primary-600"></i>
                        </div>
                        <div class="flex-grow-1">
                            <h1 class="h2 mb-2">Senior Software Developer</h1>
                            <h5 class="text-gray-500 mb-3"><i class="fas fa-building me-2"></i>TechCorp Solutions</h5>
                            <div class="job-meta mb-3">
                                <span class="badge bg-primary-600 me-2"><i class="fas fa-map-marker-alt me-1"></i>New York, NY</span>
                                <span class="badge bg-green-600 me-2"><i class="fas fa-clock me-1"></i>Full-time</span>
                                <span class="badge bg-info me-2"><i class="fas fa-dollar-sign me-1"></i>$80k-$120k</span>
                                <span class="badge bg-yellow-500"><i class="fas fa-calendar me-1"></i>Posted 3 days ago</span>
                            </div>
                            <p class="lead mb-0">Join our innovative team and help build cutting-edge software solutions that impact millions of users worldwide.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Job Description -->
            <div class="bg-white shadow rounded-lg overflow-hidden shadow-sm mb-4">
                <div class="bg-white shadow rounded-lg overflow-hidden -header">
                    <h5 class="mb-0"><i class="fas fa-file-alt me-2"></i>Job Description</h5>
                </div>
                <div class="bg-white shadow rounded-lg overflow-hidden -body">
                    <h6>About the Role</h6>
                    <p>We are seeking a talented Senior Software Developer to join our growing engineering team. You will be responsible for designing, developing, and maintaining high-quality software applications using modern technologies and best practices.</p>
                    
                    <h6 class="mt-4">Key Responsibilities</h6>
                    <ul>
                        <li>Design and develop scalable web applications using modern frameworks</li>
                        <li>Collaborate with cross-functional teams to define, design, and ship new features</li>
                        <li>Write clean, maintainable, and well-documented code</li>
                        <li>Participate in code reviews and mentor junior developers</li>
                        <li>Troubleshoot and debug applications to ensure optimal performance</li>
                        <li>Stay up-to-date with emerging technologies and industry trends</li>
                    </ul>

                    <h6 class="mt-4">Required Qualifications</h6>
                    <ul>
                        <li>Bachelor's degree in Computer Science or related field</li>
                        <li>5+ years of experience in software development</li>
                        <li>Strong proficiency in JavaScript, Python, or Java</li>
                        <li>Experience with modern web frameworks (React, Vue.js, Angular)</li>
                        <li>Knowledge of database design and SQL</li>
                        <li>Experience with version control systems (Git)</li>
                        <li>Strong problem-solving and communication skills</li>
                    </ul>

                    <h6 class="mt-4">Preferred Qualifications</h6>
                    <ul>
                        <li>Experience with cloud platforms (AWS, Azure, GCP)</li>
                        <li>Knowledge of containerization (Docker, Kubernetes)</li>
                        <li>Experience with CI/CD pipelines</li>
                        <li>Understanding of microservices architecture</li>
                        <li>Experience with agile development methodologies</li>
                    </ul>

                    <h6 class="mt-4">What We Offer</h6>
                    <ul>
                        <li>Competitive salary and equity package</li>
                        <li>Comprehensive health, dental, and vision insurance</li>
                        <li>Flexible work arrangements and remote work options</li>
                        <li>Professional development opportunities</li>
                        <li>401(k) with company matching</li>
                        <li>Unlimited PTO policy</li>
                        <li>Modern office with great amenities</li>
                    </ul>
                </div>
            </div>

            <!-- Required Skills -->
            <div class="bg-white shadow rounded-lg overflow-hidden shadow-sm mb-4">
                <div class="bg-white shadow rounded-lg overflow-hidden -header">
                    <h5 class="mb-0"><i class="fas fa-cogs me-2"></i>Required Skills</h5>
                </div>
                <div class="bg-white shadow rounded-lg overflow-hidden -body">
                    <div class="flex flex-wrap gap-2">
                        <span class="badge bg-primary-600 p-2">JavaScript</span>
                        <span class="badge bg-primary-600 p-2">React</span>
                        <span class="badge bg-primary-600 p-2">Node.js</span>
                        <span class="badge bg-primary-600 p-2">Python</span>
                        <span class="badge bg-primary-600 p-2">SQL</span>
                        <span class="badge bg-primary-600 p-2">Git</span>
                        <span class="badge bg-secondary p-2">AWS</span>
                        <span class="badge bg-secondary p-2">Docker</span>
                        <span class="badge bg-secondary p-2">REST APIs</span>
                        <span class="badge bg-secondary p-2">Agile</span>
                    </div>
                </div>
            </div>

            <!-- Company Information -->
            <div class="bg-white shadow rounded-lg overflow-hidden shadow-sm">
                <div class="bg-white shadow rounded-lg overflow-hidden -header">
                    <h5 class="mb-0"><i class="fas fa-building me-2"></i>About TechCorp Solutions</h5>
                </div>
                <div class="bg-white shadow rounded-lg overflow-hidden -body">
                    <p>TechCorp Solutions is a leading technology company specializing in innovative software solutions for businesses worldwide. Founded in 2015, we have grown to over 500 employees across multiple offices globally.</p>
                    
                    <div class="flex flex-wrap mt-4">
                        <div class="flex-1 -md-6">
                            <div class="company-stat">
                                <i class="fas fa-users fa-2x text-primary-600 mb-2"></i>
                                <h6>500+ Employees</h6>
                            </div>
                        </div>
                        <div class="flex-1 -md-6">
                            <div class="company-stat">
                                <i class="fas fa-calendar fa-2x text-primary-600 mb-2"></i>
                                <h6>Founded in 2015</h6>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-3">
                        <a href="{{ route('company.show', 1) }}" class="btn px-4 py-2 rounded font-medium transition-colors -outline-primary">
                            <i class="fas fa-external-link-alt me-2"></i>View Company Profile
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="flex-1 -lg-4">
            <!-- Apply Card -->
            <div class="card shadow-sm mb-4 apply- bg-white shadow rounded-lg overflow-hidden">
                <div class="bg-white shadow rounded-lg overflow-hidden -body text-center p-4">
                    <h5 class="mb-3">Ready to Apply?</h5>
                    <p class="text-gray-500 mb-4">Join our team and start your career journey with us!</p>
                    <button class="btn bg-primary-600 text-white hover: bg-primary-600 -700 px-4 py-2 rounded font-medium transition-colors -lg w-full mb-3" onclick="showApplyModal()">
                        <i class="fas fa-paper-plane me-2"></i>Apply Now
                    </button>
                    <button class="btn px-4 py-2 rounded font-medium transition-colors -outline-danger w-full" onclick="toggleSaveJob()">
                        <i class="far fa-heart me-2"></i>Save Job
                    </button>
                </div>
            </div>

            <!-- Job Summary -->
            <div class="bg-white shadow rounded-lg overflow-hidden shadow-sm mb-4">
                <div class="bg-white shadow rounded-lg overflow-hidden -header">
                    <h6 class="mb-0"><i class="fas fa-info-circle me-2"></i>Job Summary</h6>
                </div>
                <div class="bg-white shadow rounded-lg overflow-hidden -body">
                    <div class="summary-item mb-3">
                        <strong>Job ID:</strong> JOB-2024-001
                    </div>
                    <div class="summary-item mb-3">
                        <strong>Department:</strong> Engineering
                    </div>
                    <div class="summary-item mb-3">
                        <strong>Experience:</strong> 5+ years
                    </div>
                    <div class="summary-item mb-3">
                        <strong>Education:</strong> Bachelor's Degree
                    </div>
                    <div class="summary-item mb-3">
                        <strong>Employment Type:</strong> Full-time
                    </div>
                    <div class="summary-item mb-3">
                        <strong>Location:</strong> New York, NY
                    </div>
                    <div class="summary-item">
                        <strong>Salary:</strong> $80,000 - $120,000
                    </div>
                </div>
            </div>

            <!-- Share Job -->
            <div class="bg-white shadow rounded-lg overflow-hidden shadow-sm">
                <div class="bg-white shadow rounded-lg overflow-hidden -header">
                    <h6 class="mb-0"><i class="fas fa-share-alt me-2"></i>Share this Job</h6>
                </div>
                <div class="bg-white shadow rounded-lg overflow-hidden -body">
                    <div class="flex gap-2">
                        <button class="btn btn-outline-primary px-4 py-2 rounded font-medium transition-colors -sm flex-fill" onclick="shareJob('facebook')">
                            <i class="fab fa-facebook-f"></i>
                        </button>
                        <button class="btn btn-outline-info px-4 py-2 rounded font-medium transition-colors -sm flex-fill" onclick="shareJob('twitter')">
                            <i class="fab fa-twitter"></i>
                        </button>
                        <button class="btn btn-outline-primary px-4 py-2 rounded font-medium transition-colors -sm flex-fill" onclick="shareJob('linkedin')">
                            <i class="fab fa-linkedin-in"></i>
                        </button>
                        <button class="btn btn-outline-secondary px-4 py-2 rounded font-medium transition-colors -sm flex-fill" onclick="copyJobLink()">
                            <i class="fas fa-link"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Apply Modal -->
<div class="modal fade" id="applyModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Apply for Senior Software Developer</h5>
                <button type="button" class="px-4 py-2 rounded font-medium transition-colors -close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="applyForm">
                    <div class="flex flex-wrap">
                        <div class="flex-1 -md-6 mb-3">
                            <label for="firstName" class="block text-sm font-medium text-gray-700 mb-1">First Name *</label>
                            <input type="text" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500" id="firstName" required>
                        </div>
                        <div class="flex-1 -md-6 mb-3">
                            <label for="lastName" class="block text-sm font-medium text-gray-700 mb-1">Last Name *</label>
                            <input type="text" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500" id="lastName" required>
                        </div>
                    </div>
                    
                    <div class="flex flex-wrap">
                        <div class="flex-1 -md-6 mb-3">
                            <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email Address *</label>
                            <input type="email" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500" id="email" required>
                        </div>
                        <div class="flex-1 -md-6 mb-3">
                            <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">Phone Number *</label>
                            <input type="tel" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500" id="phone" required>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="resume" class="block text-sm font-medium text-gray-700 mb-1">Upload Resume *</label>
                        <input type="file" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500" id="resume" accept=".pdf,.doc,.docx" required>
                        <div class="form-text">Accepted formats: PDF, DOC, DOCX (Max 5MB)</div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="coverLetter" class="block text-sm font-medium text-gray-700 mb-1">Cover Letter</label>
                        <textarea class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500" id="coverLetter" rows="4" placeholder="Tell us why you're interested in this position..."></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn px-4 py-2 rounded font-medium transition-colors -secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn px-4 py-2 rounded font-medium transition-colors -primary" onclick="submitApplication()">
                    <i class="fas fa-paper-plane me-2"></i>Submit Application
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('page_scripts')
<script>
function showApplyModal() {
    const modal = new bootstrap.Modal(document.getElementById('applyModal'));
    modal.show();
}

function toggleSaveJob() {
    const btn = event.target.closest('button');
    const icon = btn.querySelector('i');
    
    if (icon.classList.contains('far')) {
        icon.classList.remove('far');
        icon.classList.add('fas');
        btn.classList.remove('btn-outline-danger');
        btn.classList.add('btn-danger');
        btn.innerHTML = '<i class="fas fa-heart me-2"></i>Job Saved';
    } else {
        icon.classList.remove('fas');
        icon.classList.add('far');
        btn.classList.remove('btn-danger');
        btn.classList.add('btn-outline-danger');
        btn.innerHTML = '<i class="far fa-heart me-2"></i>Save Job';
    }
}

function shareJob(platform) {
    const url = window.location.href;
    const title = 'Senior Software Developer at TechCorp Solutions';
    
    let shareUrl = '';
    switch(platform) {
        case 'facebook':
            shareUrl = `https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(url)}`;
            break;
        case 'twitter':
            shareUrl = `https://twitter.com/intent/tweet?url=${encodeURIComponent(url)}&text=${encodeURIComponent(title)}`;
            break;
        case 'linkedin':
            shareUrl = `https://www.linkedin.com/sharing/share-offsite/?url=${encodeURIComponent(url)}`;
            break;
    }
    
    if (shareUrl) {
        window.open(shareUrl, '_blank', 'width=600,height=400');
    }
}

function copyJobLink() {
    navigator.clipboard.writeText(window.location.href).then(() => {
        alert('Job link copied to clipboard!');
    });
}

function submitApplication() {
    const form = document.getElementById('applyForm');
    if (form.checkValidity()) {
        // Simulate form submission
        const btn = event.target;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Submitting...';
        btn.disabled = true;
        
        setTimeout(() => {
            alert('Application submitted successfully! We\'ll be in touch soon.');
            bootstrap.Modal.getInstance(document.getElementById('applyModal')).hide();
            form.reset();
            btn.innerHTML = '<i class="fas fa-paper-plane me-2"></i>Submit Application';
            btn.disabled = false;
        }, 2000);
    } else {
        form.reportValidity();
    }
}
</script>
@endsection
