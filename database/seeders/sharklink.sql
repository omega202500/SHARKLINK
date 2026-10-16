-- =========================================================================
-- SHARKLINK - Schéma de Base de Données PostgreSQL (V1)
-- =========================================================================

-- Extension pour la génération d'UUID si nécessaire
CREATE EXTENSION IF NOT EXISTS "uuid-ossp";

-- =========================================================================
-- MODULE A — Établissements et abonnements
-- =========================================================================

CREATE TABLE establishments (
    id BIGSERIAL PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    code VARCHAR(100) NOT NULL UNIQUE,
    slug VARCHAR(100) NOT NULL UNIQUE,
    email VARCHAR(255) NOT NULL,
    phone VARCHAR(50),
    address TEXT,
    city VARCHAR(100),
    country VARCHAR(100),
    logo_path VARCHAR(255),
    status VARCHAR(50) NOT NULL DEFAULT 'ACTIF', -- StatutÉtablissement
    created_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE campuses (
    id BIGSERIAL PRIMARY KEY,
    establishment_id BIGINT NOT NULL REFERENCES establishments(id) ON DELETE CASCADE,
    name VARCHAR(255) NOT NULL,
    code VARCHAR(50) NOT NULL,
    address TEXT,
    city VARCHAR(100),
    is_active BOOLEAN NOT NULL DEFAULT TRUE,
    CONSTRAINT fk_campus_establishment FOREIGN KEY (establishment_id) REFERENCES establishments(id)
);

CREATE TABLE academic_years (
    id BIGSERIAL PRIMARY KEY,
    establishment_id BIGINT NOT NULL REFERENCES establishments(id) ON DELETE CASCADE,
    name VARCHAR(100) NOT NULL,
    start_date DATE NOT NULL,
    end_date DATE NOT NULL,
    status VARCHAR(50) NOT NULL,
    is_current BOOLEAN NOT NULL DEFAULT FALSE,
    CONSTRAINT chk_academic_dates CHECK (end_date > start_date),
    CONSTRAINT uk_establishment_academic_year UNIQUE (establishment_id, name)
);

CREATE TABLE subscription_plans (
    id BIGSERIAL PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    code VARCHAR(50) NOT NULL UNIQUE,
    description TEXT,
    price NUMERIC(12,2) NOT NULL CHECK (price >= 0),
    billing_period VARCHAR(50) NOT NULL,
    limits_json JSONB,
    is_active BOOLEAN NOT NULL DEFAULT TRUE
);

CREATE TABLE subscriptions (
    id BIGSERIAL PRIMARY KEY,
    establishment_id BIGINT NOT NULL REFERENCES establishments(id) ON DELETE CASCADE,
    subscription_plan_id BIGINT NOT NULL REFERENCES subscription_plans(id),
    starts_at DATE NOT NULL,
    ends_at DATE NOT NULL,
    status VARCHAR(50) NOT NULL, -- StatutAbonnement
    auto_renew BOOLEAN NOT NULL DEFAULT TRUE,
    CONSTRAINT chk_sub_dates CHECK (ends_at > starts_at)
);

CREATE TABLE establishment_settings (
    id BIGSERIAL PRIMARY KEY,
    establishment_id BIGINT NOT NULL REFERENCES establishments(id) ON DELETE CASCADE,
    setting_key VARCHAR(100) NOT NULL,
    setting_value JSONB,
    CONSTRAINT uk_establishment_setting UNIQUE (establishment_id, setting_key)
);


-- =========================================================================
-- MODULE B — Utilisateurs, profils et autorisations
-- =========================================================================

CREATE TABLE users (
    id BIGSERIAL PRIMARY KEY,
    public_id UUID NOT NULL DEFAULT uuid_generate_v4() UNIQUE,
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    phone VARCHAR(50),
    password VARCHAR(255) NOT NULL,
    avatar_path VARCHAR(255),
    status VARCHAR(50) NOT NULL DEFAULT 'ACTIF',
    email_verified_at TIMESTAMP WITH TIME ZONE,
    last_login_at TIMESTAMP WITH TIME ZONE,
    created_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE establishment_memberships (
    id BIGSERIAL PRIMARY KEY,
    establishment_id BIGINT NOT NULL REFERENCES establishments(id) ON DELETE CASCADE,
    user_id BIGINT NOT NULL REFERENCES users(id) ON DELETE CASCADE,
    member_number VARCHAR(100),
    status VARCHAR(50) NOT NULL DEFAULT 'ACTIF',
    joined_at DATE NOT NULL DEFAULT CURRENT_DATE,
    CONSTRAINT uk_establishment_user UNIQUE (establishment_id, user_id)
);

CREATE TABLE student_profiles (
    id BIGSERIAL PRIMARY KEY,
    membership_id BIGINT NOT NULL UNIQUE REFERENCES establishment_memberships(id) ON DELETE CASCADE,
    date_of_birth DATE,
    gender VARCHAR(20),
    nationality VARCHAR(100),
    address TEXT,
    emergency_contact VARCHAR(255),
    student_number VARCHAR(100) NOT NULL
);

CREATE TABLE staff_profiles (
    id BIGSERIAL PRIMARY KEY,
    membership_id BIGINT NOT NULL UNIQUE REFERENCES establishment_memberships(id) ON DELETE CASCADE,
    staff_number VARCHAR(100) NOT NULL,
    job_title VARCHAR(150),
    hire_date DATE,
    employment_status VARCHAR(50)
);

CREATE TABLE roles (
    id BIGSERIAL PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    code VARCHAR(50) NOT NULL UNIQUE,
    scope VARCHAR(50) NOT NULL, -- ex: 'PLATFORM' ou 'ESTABLISHMENT'
    description TEXT
);

CREATE TABLE permissions (
    id BIGSERIAL PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    code VARCHAR(50) NOT NULL UNIQUE,
    description TEXT
);

CREATE TABLE membership_roles (
    membership_id BIGINT NOT NULL REFERENCES establishment_memberships(id) ON DELETE CASCADE,
    role_id BIGINT NOT NULL REFERENCES roles(id) ON DELETE CASCADE,
    assigned_by BIGINT REFERENCES users(id),
    assigned_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (membership_id, role_id)
);

CREATE TABLE role_permissions (
    role_id BIGINT NOT NULL REFERENCES roles(id) ON DELETE CASCADE,
    permission_id BIGINT NOT NULL REFERENCES permissions(id) ON DELETE CASCADE,
    PRIMARY KEY (role_id, permission_id)
);

CREATE TABLE platform_user_roles (
    user_id BIGINT NOT NULL REFERENCES users(id) ON DELETE CASCADE,
    role_id BIGINT NOT NULL REFERENCES roles(id) ON DELETE CASCADE,
    PRIMARY KEY (user_id, role_id)
);


-- =========================================================================
-- MODULE C — Organisation académique et admissions
-- =========================================================================

CREATE TABLE faculties (
    id BIGSERIAL PRIMARY KEY,
    establishment_id BIGINT NOT NULL REFERENCES establishments(id) ON DELETE CASCADE,
    name VARCHAR(255) NOT NULL,
    code VARCHAR(50) NOT NULL,
    description TEXT
);

CREATE TABLE departments (
    id BIGSERIAL PRIMARY KEY,
    establishment_id BIGINT NOT NULL REFERENCES establishments(id) ON DELETE CASCADE,
    faculty_id BIGINT REFERENCES faculties(id) ON DELETE SET NULL,
    name VARCHAR(255) NOT NULL,
    code VARCHAR(50) NOT NULL
);

CREATE TABLE programs (
    id BIGSERIAL PRIMARY KEY,
    establishment_id BIGINT NOT NULL REFERENCES establishments(id) ON DELETE CASCADE,
    department_id BIGINT NOT NULL REFERENCES departments(id) ON DELETE CASCADE,
    name VARCHAR(255) NOT NULL,
    code VARCHAR(50) NOT NULL,
    degree_type VARCHAR(100),
    duration_years INT NOT NULL CHECK (duration_years > 0),
    grading_system VARCHAR(100)
);

CREATE TABLE levels (
    id BIGSERIAL PRIMARY KEY,
    establishment_id BIGINT NOT NULL REFERENCES establishments(id) ON DELETE CASCADE,
    name VARCHAR(100) NOT NULL,
    code VARCHAR(50) NOT NULL,
    level_order INT NOT NULL
);

CREATE TABLE cohorts (
    id BIGSERIAL PRIMARY KEY,
    establishment_id BIGINT NOT NULL REFERENCES establishments(id) ON DELETE CASCADE,
    program_id BIGINT NOT NULL REFERENCES programs(id) ON DELETE CASCADE,
    level_id BIGINT NOT NULL REFERENCES levels(id) ON DELETE CASCADE,
    academic_year_id BIGINT NOT NULL REFERENCES academic_years(id) ON DELETE CASCADE,
    name VARCHAR(150) NOT NULL,
    capacity INT CHECK (capacity >= 0)
);

CREATE TABLE admission_applications (
    id BIGSERIAL PRIMARY KEY,
    establishment_id BIGINT NOT NULL REFERENCES establishments(id) ON DELETE CASCADE,
    academic_year_id BIGINT NOT NULL REFERENCES academic_years(id) ON DELETE CASCADE,
    program_id BIGINT NOT NULL REFERENCES programs(id) ON DELETE CASCADE,
    applicant_user_id BIGINT REFERENCES users(id) ON DELETE SET NULL,
    application_number VARCHAR(100) NOT NULL,
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    email VARCHAR(255) NOT NULL,
    phone VARCHAR(50),
    status VARCHAR(50) NOT NULL DEFAULT 'SOUMISE', -- StatutCandidature
    submitted_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP,
    reviewed_by BIGINT REFERENCES users(id),
    CONSTRAINT uk_establishment_app_number UNIQUE (establishment_id, application_number)
);

CREATE TABLE admission_documents (
    id BIGSERIAL PRIMARY KEY,
    application_id BIGINT NOT NULL REFERENCES admission_applications(id) ON DELETE CASCADE,
    document_type VARCHAR(100) NOT NULL,
    file_path VARCHAR(255) NOT NULL,
    verification_status VARCHAR(50),
    verified_by BIGINT REFERENCES users(id),
    verified_at TIMESTAMP WITH TIME ZONE
);

CREATE TABLE student_enrollments (
    id BIGSERIAL PRIMARY KEY,
    establishment_id BIGINT NOT NULL REFERENCES establishments(id) ON DELETE CASCADE,
    student_membership_id BIGINT NOT NULL REFERENCES establishment_memberships(id) ON DELETE CASCADE,
    academic_year_id BIGINT NOT NULL REFERENCES academic_years(id) ON DELETE CASCADE,
    enrollment_number VARCHAR(100) NOT NULL,
    enrolled_at DATE NOT NULL DEFAULT CURRENT_DATE,
    status VARCHAR(50) NOT NULL DEFAULT 'VALFFEE', -- StatutInscription
    CONSTRAINT uk_student_academic_year UNIQUE (student_membership_id, academic_year_id)
);

CREATE TABLE program_enrollments (
    id BIGSERIAL PRIMARY KEY,
    student_enrollment_id BIGINT NOT NULL REFERENCES student_enrollments(id) ON DELETE CASCADE,
    cohort_id BIGINT NOT NULL REFERENCES cohorts(id),
    program_id BIGINT NOT NULL REFERENCES programs(id),
    level_id BIGINT NOT NULL REFERENCES levels(id),
    status VARCHAR(50) NOT NULL
);


-- =========================================================================
-- MODULE D — Enseignements, cours et évaluations
-- =========================================================================

CREATE TABLE courses (
    id BIGSERIAL PRIMARY KEY,
    establishment_id BIGINT NOT NULL REFERENCES establishments(id) ON DELETE CASCADE,
    code VARCHAR(50) NOT NULL,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    default_credits INT NOT NULL DEFAULT 0,
    default_coefficient NUMERIC(5,2) NOT NULL DEFAULT 1.00
);

CREATE TABLE teaching_units (
    id BIGSERIAL PRIMARY KEY,
    establishment_id BIGINT NOT NULL REFERENCES establishments(id) ON DELETE CASCADE,
    code VARCHAR(50) NOT NULL,
    title VARCHAR(255) NOT NULL,
    credits INT NOT NULL DEFAULT 0,
    description TEXT
);

CREATE TABLE teaching_unit_courses (
    teaching_unit_id BIGINT NOT NULL REFERENCES teaching_units(id) ON DELETE CASCADE,
    course_id BIGINT NOT NULL REFERENCES courses(id) ON DELETE CASCADE,
    coefficient NUMERIC(5,2) NOT NULL DEFAULT 1.00,
    sort_order INT DEFAULT 0,
    PRIMARY KEY (teaching_unit_id, course_id)
);

CREATE TABLE course_offerings (
    id BIGSERIAL PRIMARY KEY,
    establishment_id BIGINT NOT NULL REFERENCES establishments(id) ON DELETE CASCADE,
    course_id BIGINT NOT NULL REFERENCES courses(id) ON DELETE CASCADE,
    cohort_id BIGINT NOT NULL REFERENCES cohorts(id) ON DELETE CASCADE,
    academic_year_id BIGINT NOT NULL REFERENCES academic_years(id) ON DELETE CASCADE,
    term VARCHAR(50),
    status VARCHAR(50) NOT NULL
);

CREATE TABLE teacher_assignments (
    id BIGSERIAL PRIMARY KEY,
    course_offering_id BIGINT NOT NULL REFERENCES course_offerings(id) ON DELETE CASCADE,
    teacher_membership_id BIGINT NOT NULL REFERENCES establishment_memberships(id) ON DELETE CASCADE,
    assignment_type VARCHAR(50) NOT NULL
);

CREATE TABLE course_materials (
    id BIGSERIAL PRIMARY KEY,
    course_offering_id BIGINT NOT NULL REFERENCES course_offerings(id) ON DELETE CASCADE,
    uploaded_by_membership_id BIGINT NOT NULL REFERENCES establishment_memberships(id),
    title VARCHAR(255) NOT NULL,
    file_path VARCHAR(255) NOT NULL,
    visibility VARCHAR(50) NOT NULL,
    published_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE assignments (
    id BIGSERIAL PRIMARY KEY,
    course_offering_id BIGINT NOT NULL REFERENCES course_offerings(id) ON DELETE CASCADE,
    title VARCHAR(255) NOT NULL,
    instructions TEXT,
    due_at TIMESTAMP WITH TIME ZONE NOT NULL,
    max_score NUMERIC(5,2) NOT NULL,
    status VARCHAR(50) NOT NULL
);

CREATE TABLE assignment_submissions (
    id BIGSERIAL PRIMARY KEY,
    assignment_id BIGINT NOT NULL REFERENCES assignments(id) ON DELETE CASCADE,
    student_membership_id BIGINT NOT NULL REFERENCES establishment_memberships(id) ON DELETE CASCADE,
    submitted_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP,
    file_path VARCHAR(255),
    text_content TEXT,
    status VARCHAR(50) NOT NULL,
    feedback TEXT,
    graded_by BIGINT REFERENCES establishment_memberships(id),
    score NUMERIC(5,2),
    CONSTRAINT uk_assignment_student UNIQUE (assignment_id, student_membership_id)
);

CREATE TABLE assessments (
    id BIGSERIAL PRIMARY KEY,
    course_offering_id BIGINT NOT NULL REFERENCES course_offerings(id) ON DELETE CASCADE,
    title VARCHAR(255) NOT NULL,
    assessment_type VARCHAR(50) NOT NULL,
    assessment_date DATE NOT NULL,
    max_score NUMERIC(5,2) NOT NULL,
    coefficient NUMERIC(5,2) NOT NULL DEFAULT 1.00,
    status VARCHAR(50) NOT NULL
);

CREATE TABLE grades (
    id BIGSERIAL PRIMARY KEY,
    assessment_id BIGINT NOT NULL REFERENCES assessments(id) ON DELETE CASCADE,
    student_membership_id BIGINT NOT NULL REFERENCES establishment_memberships(id) ON DELETE CASCADE,
    score NUMERIC(5,2) NOT NULL CHECK (score >= 0),
    status VARCHAR(50) NOT NULL, -- StatutNote: BROUILLON, VALIDEE, PUBLIEE
    entered_by BIGINT NOT NULL REFERENCES establishment_memberships(id),
    validated_by BIGINT REFERENCES establishment_memberships(id),
    validated_at TIMESTAMP WITH TIME ZONE,
    CONSTRAINT uk_assessment_student_grade UNIQUE (assessment_id, student_membership_id)
);

CREATE TABLE grade_publications (
    id BIGSERIAL PRIMARY KEY,
    course_offering_id BIGINT NOT NULL REFERENCES course_offerings(id) ON DELETE CASCADE,
    published_by BIGINT NOT NULL REFERENCES establishment_memberships(id),
    published_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP,
    status VARCHAR(50) NOT NULL
);

CREATE TABLE deliberations (
    id BIGSERIAL PRIMARY KEY,
    establishment_id BIGINT NOT NULL REFERENCES establishments(id) ON DELETE CASCADE,
    cohort_id BIGINT NOT NULL REFERENCES cohorts(id) ON DELETE CASCADE,
    academic_year_id BIGINT NOT NULL REFERENCES academic_years(id) ON DELETE CASCADE,
    term VARCHAR(50),
    held_at DATE NOT NULL,
    status VARCHAR(50) NOT NULL,
    approved_by BIGINT REFERENCES establishment_memberships(id)
);

CREATE TABLE deliberation_results (
    id BIGSERIAL PRIMARY KEY,
    deliberation_id BIGINT NOT NULL REFERENCES deliberations(id) ON DELETE CASCADE,
    student_enrollment_id BIGINT NOT NULL REFERENCES student_enrollments(id) ON DELETE CASCADE,
    average NUMERIC(5,2) NOT NULL,
    credits_earned INT NOT NULL,
    decision VARCHAR(100) NOT NULL,
    comment TEXT
);


-- =========================================================================
-- MODULE E — Emplois du temps et présence
-- =========================================================================

CREATE TABLE classrooms (
    id BIGSERIAL PRIMARY KEY,
    establishment_id BIGINT NOT NULL REFERENCES establishments(id) ON DELETE CASCADE,
    campus_id BIGINT REFERENCES campuses(id) ON DELETE SET NULL,
    name VARCHAR(150) NOT NULL,
    capacity INT CHECK (capacity >= 0),
    room_type VARCHAR(50)
);

CREATE TABLE timetable_entries (
    id BIGSERIAL PRIMARY KEY,
    course_offering_id BIGINT NOT NULL REFERENCES course_offerings(id) ON DELETE CASCADE,
    classroom_id BIGINT REFERENCES classrooms(id) ON DELETE SET NULL,
    starts_at TIMESTAMP WITH TIME ZONE NOT NULL,
    ends_at TIMESTAMP WITH TIME ZONE NOT NULL,
    session_type VARCHAR(50) NOT NULL,
    status VARCHAR(50) NOT NULL,
    CONSTRAINT chk_timetable_time CHECK (ends_at > starts_at)
);

CREATE TABLE attendance_sessions (
    id BIGSERIAL PRIMARY KEY,
    timetable_entry_id BIGINT NOT NULL REFERENCES timetable_entries(id) ON DELETE CASCADE,
    opened_by BIGINT NOT NULL REFERENCES establishment_memberships(id),
    opened_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP,
    closed_at TIMESTAMP WITH TIME ZONE,
    status VARCHAR(50) NOT NULL
);

CREATE TABLE attendance_records (
    id BIGSERIAL PRIMARY KEY,
    attendance_session_id BIGINT NOT NULL REFERENCES attendance_sessions(id) ON DELETE CASCADE,
    student_membership_id BIGINT NOT NULL REFERENCES establishment_memberships(id) ON DELETE CASCADE,
    attendance_status VARCHAR(50) NOT NULL, -- StatutPresence: PRESENT, ABSENT, RETARD, JUSTIFIE
    recorded_by BIGINT NOT NULL REFERENCES establishment_memberships(id),
    comment TEXT,
    CONSTRAINT uk_session_student_attendance UNIQUE (attendance_session_id, student_membership_id)
);


-- =========================================================================
-- MODULE F — Frais de scolarité et paiements
-- =========================================================================

CREATE TABLE fee_types (
    id BIGSERIAL PRIMARY KEY,
    establishment_id BIGINT NOT NULL REFERENCES establishments(id) ON DELETE CASCADE,
    name VARCHAR(150) NOT NULL,
    code VARCHAR(50) NOT NULL,
    description TEXT
);

CREATE TABLE student_fee_assignments (
    id BIGSERIAL PRIMARY KEY,
    student_enrollment_id BIGINT NOT NULL REFERENCES student_enrollments(id) ON DELETE CASCADE,
    fee_type_id BIGINT NOT NULL REFERENCES fee_types(id) ON DELETE CASCADE,
    amount_due NUMERIC(12,2) NOT NULL CHECK (amount_due >= 0),
    due_date DATE NOT NULL,
    status VARCHAR(50) NOT NULL
);

CREATE TABLE invoices (
    id BIGSERIAL PRIMARY KEY,
    establishment_id BIGINT NOT NULL REFERENCES establishments(id) ON DELETE CASCADE,
    student_enrollment_id BIGINT NOT NULL REFERENCES student_enrollments(id) ON DELETE CASCADE,
    invoice_number VARCHAR(100) NOT NULL,
    issued_at DATE NOT NULL DEFAULT CURRENT_DATE,
    due_date DATE NOT NULL,
    total_amount NUMERIC(12,2) NOT NULL CHECK (total_amount >= 0),
    status VARCHAR(50) NOT NULL,
    CONSTRAINT uk_establishment_invoice UNIQUE (establishment_id, invoice_number)
);

CREATE TABLE invoice_items (
    id BIGSERIAL PRIMARY KEY,
    invoice_id BIGINT NOT NULL REFERENCES invoices(id) ON DELETE CASCADE,
    fee_type_id BIGINT REFERENCES fee_types(id) ON DELETE SET NULL,
    description TEXT NOT NULL,
    quantity INT NOT NULL DEFAULT 1 CHECK (quantity > 0),
    unit_price NUMERIC(12,2) NOT NULL CHECK (unit_price >= 0),
    line_total NUMERIC(12,2) NOT NULL CHECK (line_total >= 0)
);

CREATE TABLE payments (
    id BIGSERIAL PRIMARY KEY,
    establishment_id BIGINT NOT NULL REFERENCES establishments(id) ON DELETE CASCADE,
    student_enrollment_id BIGINT NOT NULL REFERENCES student_enrollments(id) ON DELETE CASCADE,
    payment_reference VARCHAR(100) NOT NULL UNIQUE,
    amount NUMERIC(12,2) NOT NULL CHECK (amount > 0),
    currency VARCHAR(3) NOT NULL DEFAULT 'XAF',
    payment_method VARCHAR(50) NOT NULL, -- StatutPaiement / ModePaiement
    status VARCHAR(50) NOT NULL,
    received_at TIMESTAMP WITH TIME ZONE NOT NULL DEFAULT CURRENT_TIMESTAMP,
    received_by BIGINT REFERENCES establishment_memberships(id)
);

CREATE TABLE payment_transactions (
    id BIGSERIAL PRIMARY KEY,
    payment_id BIGINT NOT NULL REFERENCES payments(id) ON DELETE CASCADE,
    provider VARCHAR(100) NOT NULL,
    provider_transaction_id VARCHAR(255) NOT NULL,
    idempotency_key VARCHAR(255) NOT NULL UNIQUE,
    status VARCHAR(50) NOT NULL,
    request_payload JSONB,
    response_payload JSONB,
    confirmed_at TIMESTAMP WITH TIME ZONE
);

CREATE TABLE payment_allocations (
    id BIGSERIAL PRIMARY KEY,
    payment_id BIGINT NOT NULL REFERENCES payments(id) ON DELETE CASCADE,
    invoice_id BIGINT NOT NULL REFERENCES invoices(id) ON DELETE CASCADE,
    allocated_amount NUMERIC(12,2) NOT NULL CHECK (allocated_amount > 0),
    CONSTRAINT uk_payment_invoice UNIQUE (payment_id, invoice_id)
);

CREATE TABLE receipts (
    id BIGSERIAL PRIMARY KEY,
    payment_id BIGINT NOT NULL UNIQUE REFERENCES payments(id) ON DELETE CASCADE,
    receipt_number VARCHAR(100) NOT NULL UNIQUE,
    issued_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP,
    file_path VARCHAR(255) NOT NULL,
    status VARCHAR(50) NOT NULL
);


-- =========================================================================
-- MODULE G — Communication et démarches administratives
-- =========================================================================

CREATE TABLE announcements (
    id BIGSERIAL PRIMARY KEY,
    establishment_id BIGINT NOT NULL REFERENCES establishments(id) ON DELETE CASCADE,
    author_membership_id BIGINT NOT NULL REFERENCES establishment_memberships(id),
    title VARCHAR(255) NOT NULL,
    body TEXT NOT NULL,
    target_type VARCHAR(100) NOT NULL,
    published_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP,
    expires_at TIMESTAMP WITH TIME ZONE,
    status VARCHAR(50) NOT NULL
);

CREATE TABLE conversations (
    id BIGSERIAL PRIMARY KEY,
    establishment_id BIGINT REFERENCES establishments(id) ON DELETE CASCADE,
    conversation_type VARCHAR(50) NOT NULL,
    title VARCHAR(255),
    created_by BIGINT NOT NULL REFERENCES users(id),
    created_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE conversation_participants (
    conversation_id BIGINT NOT NULL REFERENCES conversations(id) ON DELETE CASCADE,
    user_id BIGINT NOT NULL REFERENCES users(id) ON DELETE CASCADE,
    joined_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP,
    last_read_at TIMESTAMP WITH TIME ZONE,
    status VARCHAR(50) NOT NULL,
    PRIMARY KEY (conversation_id, user_id)
);

CREATE TABLE messages (
    id BIGSERIAL PRIMARY KEY,
    conversation_id BIGINT NOT NULL REFERENCES conversations(id) ON DELETE CASCADE,
    sender_user_id BIGINT NOT NULL REFERENCES users(id),
    body TEXT NOT NULL,
    attachment_path VARCHAR(255),
    sent_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP,
    edited_at TIMESTAMP WITH TIME ZONE,
    deleted_at TIMESTAMP WITH TIME ZONE
);

CREATE TABLE notifications (
    id BIGSERIAL PRIMARY KEY,
    user_id BIGINT NOT NULL REFERENCES users(id) ON DELETE CASCADE,
    establishment_id BIGINT REFERENCES establishments(id) ON DELETE CASCADE,
    type VARCHAR(100) NOT NULL,
    title VARCHAR(255) NOT NULL,
    body TEXT NOT NULL,
    data JSONB,
    read_at TIMESTAMP WITH TIME ZONE,
    created_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE administrative_requests (
    id BIGSERIAL PRIMARY KEY,
    establishment_id BIGINT NOT NULL REFERENCES establishments(id) ON DELETE CASCADE,
    requester_membership_id BIGINT NOT NULL REFERENCES establishment_memberships(id),
    request_type VARCHAR(100) NOT NULL,
    description TEXT,
    status VARCHAR(50) NOT NULL, -- StatutDemande
    assigned_to BIGINT REFERENCES establishment_memberships(id),
    submitted_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP,
    processed_at TIMESTAMP WITH TIME ZONE
);

CREATE TABLE request_documents (
    id BIGSERIAL PRIMARY KEY,
    administrative_request_id BIGINT NOT NULL REFERENCES administrative_requests(id) ON DELETE CASCADE,
    document_type VARCHAR(100) NOT NULL,
    file_path VARCHAR(255) NOT NULL,
    uploaded_by BIGINT NOT NULL REFERENCES users(id),
    uploaded_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE issued_documents (
    id BIGSERIAL PRIMARY KEY,
    establishment_id BIGINT NOT NULL REFERENCES establishments(id) ON DELETE CASCADE,
    student_enrollment_id BIGINT NOT NULL REFERENCES student_enrollments(id) ON DELETE CASCADE,
    document_type VARCHAR(100) NOT NULL, -- StatutDocument (ATTESTATION, RELEVE_NOTES, DIPLOME, CARTE_ETUDIANT)
    document_number VARCHAR(100) NOT NULL UNIQUE,
    file_path VARCHAR(255) NOT NULL,
    issued_by BIGINT NOT NULL REFERENCES establishment_memberships(id),
    issued_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP,
    verification_code VARCHAR(100) NOT NULL UNIQUE
);


-- =========================================================================
-- MODULE H — Audit et sécurité
-- =========================================================================

CREATE TABLE audit_logs (
    id BIGSERIAL PRIMARY KEY,
    establishment_id BIGINT REFERENCES establishments(id) ON DELETE SET NULL,
    actor_user_id BIGINT REFERENCES users(id) ON DELETE SET NULL,
    action VARCHAR(100) NOT NULL,
    entity_type VARCHAR(100) NOT NULL,
    entity_id BIGINT,
    before_data JSONB,
    after_data JSONB,
    ip_address VARCHAR(45),
    user_agent TEXT,
    created_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP
);