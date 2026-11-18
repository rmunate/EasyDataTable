CREATE TABLE employees (
    id INTEGER PRIMARY KEY,
    company_id INTEGER NOT NULL,
    code TEXT NOT NULL,
    name TEXT NOT NULL
);
CREATE INDEX employees_company ON employees (company_id);

CREATE TABLE leave_types (
    id INTEGER PRIMARY KEY,
    name TEXT NOT NULL
);

CREATE TABLE leave_requests (
    id INTEGER PRIMARY KEY,
    employee_id INTEGER NOT NULL REFERENCES employees (id),
    leave_type_id INTEGER REFERENCES leave_types (id),
    reason TEXT NOT NULL,
    starts_on DATE NOT NULL,
    status TEXT NOT NULL
);
CREATE INDEX leave_requests_employee ON leave_requests (employee_id);

INSERT INTO employees (id, company_id, code, name) VALUES
    (1, 10, 'E-100', 'Mei Tan'),
    (2, 10, 'E-101', 'Noah Silva'),
    (3, 10, 'E-102', 'Mina Ortega'),
    (4, 20, 'E-200', 'Morgan Reed');

INSERT INTO leave_types (id, name) VALUES (1, 'Vacation'), (2, 'Personal'), (3, 'Training');

INSERT INTO leave_requests (id, employee_id, leave_type_id, reason, starts_on, status) VALUES
    (101, 1, 1, 'Family trip', '2026-10-03', 'pending'),
    (102, 2, 3, 'Certification course', '2026-10-06', 'approved'),
    (103, 3, 2, 'Personal appointment', '2026-10-08', 'rejected'),
    (104, 3, 1, 'Autumn break', '2026-10-12', 'pending'),
    (105, 1, NULL, 'Schedule change', '2026-10-14', 'approved'),
    (106, 2, 1, 'Long weekend', '2026-10-18', 'approved'),
    (107, 3, 3, 'Workshop', '2026-10-22', 'pending'),
    (201, 4, 1, 'Company 20 trip', '2026-10-25', 'pending');
