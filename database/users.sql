CREATE TABLE users (
    username VARCHAR(50) PRIMARY KEY,
    password VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(50) NOT NULL UNIQUE,
    gender ENUM("MALE", "FEMALE", "OTHER") NOT NULL,
    date_of_birth DATE,
    address TEXT,
    phone_number INTEGER(20) NOT NULL UNIQUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
)