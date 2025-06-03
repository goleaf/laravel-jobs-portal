@extends('layouts.simple')

@section('title', 'Job Details')

@section('content')
<div class="container py-5">
    <div class="row">
        <div class="col-lg-8">
            <!-- Job Header -->
            <div class="card shadow-sm mb-4">
                <div class="card-body p-4">
                    <div class="d-flex align-items-start">
                        <div class="company-logo me-4">
                            <i class="fas fa-building fa-4x text-primary"></i>
                        </div>
                        <div class="flex-grow-1">
                            <h1 class="h2 mb-2">Senior Software Developer</h1>
                            <h5 class="text-muted mb-3"><i class="fas fa-building me-2"></i>TechCorp Solutions</h5>
                            <div class="job-meta mb-3">
                                <span class="badge bg-primary me-2"><i class="fas fa-map-marker-alt me-1"></i>New York, NY</span>
                                <span class="badge bg-success me-2"><i class="fas fa-clock me-1"></i>Full-time</span>
                                <span class="badge bg-info me-2"><i class="fas fa-dollar-sign me-1"></i>$80k-$120k</span>
                                <span class="badge bg-warning"><i class="fas fa-calendar me-1"></i>Posted 3 days ago</span>
                            </div>
                            <p class="lead mb-0">Join our innovative team and help build cutting-edge software solutions that impact millions of users worldwide.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Job Description -->
            <div class="card shadow-sm mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-file-alt me-2"></i>Job Description</h5>
                </div>
                <div class="card-body">
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
            <div class="card shadow-sm mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-cogs me-2"></i>Required Skills</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex flex-wrap gap-2">
                        <span class="badge bg-primary p-2">JavaScript</span>
                        <span class="badge bg-primary p-2">React</span>
                        <span class="badge bg-primary p-2">Node.js</span>
                        <span class="badge bg-primary p-2">Python</span>
                        <span class="badge bg-primary p-2">SQL</span>
                        <span class="badge bg-primary p-2">Git</span>
                        <span class="badge bg-secondary p-2">AWS</span>
                        <span class="badge bg-secondary p-2">Docker</span>
                        <span class="badge bg-secondary p-2">REST APIs</span>
                        <span class="badge bg-secondary p-2">Agile</span>
                    </div>
                </div>
            </div>

            <!-- Company Information -->
            <div class="card shadow-sm">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-building me-2"></i>About TechCorp Solutions</h5>
                </div>
                <div class="card-body">
                    <p>TechCorp Solutions is a leading technology company specializing in innovative software solutions for businesses worldwide. Founded in 2015, we have grown to over 500 employees across multiple offices globally.</p>
                    
                    <div class="row mt-4">
                        <div class="col-md-6">
                            <div class="company-stat">
                                <i class="fas fa-users fa-2x text-primary mb-2"></i>
                                <h6>500+ Employees</h6>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="company-stat">
                                <i class="fas fa-calendar fa-2x text-primary mb-2"></i>
                                <h6>Founded in 2015</h6>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-3">
                        <a href="{{ route('company.show', 1) }}" class="btn btn-outline-primary">
                            <i class="fas fa-external-link-alt me-2"></i>View Company Profile
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Apply Card -->
            <div class="card shadow-sm mb-4 apply-card">
                <div class="card-body text-center p-4">
                    <h5 class="mb-3">Ready to Apply?</h5>
                    <p class="text-muted mb-4">Join our team and start your career journey with us!</p>
                    <button class="btn btn-primary btn-lg w-100 mb-3" onclick="showApplyModal()">
                        <i class="fas fa-paper-plane me-2"></i>Apply Now
                    </button>
                    <button class="btn btn-outline-danger w-100" onclick="toggleSaveJob()">
                        <i class="far fa-heart me-2"></i>Save Job
                    </button>
                </div>
            </div>

            <!-- Job Summary -->
            <div class="card shadow-sm mb-4">
                <div class="card-header">
                    <h6 class="mb-0"><i class="fas fa-info-circle me-2"></i>Job Summary</h6>
                </div>
                <div class="card-body">
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
            <div class="card shadow-sm">
                <div class="card-header">
                    <h6 class="mb-0"><i class="fas fa-share-alt me-2"></i>Share this Job</h6>
                </div>
                <div class="card-body">
                    <div class="d-flex gap-2">
                        <button class="btn btn-outline-primary btn-sm flex-fill" onclick="shareJob('facebook')">
                            <i class="fab fa-facebook-f"></i>
                        </button>
                        <button class="btn btn-outline-info btn-sm flex-fill" onclick="shareJob('twitter')">
                            <i class="fab fa-twitter"></i>
                        </button>
                        <button class="btn btn-outline-primary btn-sm flex-fill" onclick="shareJob('linkedin')">
                            <i class="fab fa-linkedin-in"></i>
                        </button>
                        <button class="btn btn-outline-secondary btn-sm flex-fill" onclick="copyJobLink()">
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
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="applyForm">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="firstName" class="form-label">First Name *</label>
                            <input type="text" class="form-control" id="firstName" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="lastName" class="form-label">Last Name *</label>
                            <input type="text" class="form-control" id="lastName" required>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="email" class="form-label">Email Address *</label>
                            <input type="email" class="form-control" id="email" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="phone" class="form-label">Phone Number *</label>
                            <input type="tel" class="form-control" id="phone" required>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="resume" class="form-label">Upload Resume *</label>
                        <input type="file" class="form-control" id="resume" accept=".pdf,.doc,.docx" required>
                        <div class="form-text">Accepted formats: PDF, DOC, DOCX (Max 5MB)</div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="coverLetter" class="form-label">Cover Letter</label>
                        <textarea class="form-control" id="coverLetter" rows="4" placeholder="Tell us why you're interested in this position..."></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="submitApplication()">
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
