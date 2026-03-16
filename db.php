<?php
$servername = "localhost";
$username   = "root";
$password   = "";
$dbname     = "webportfolio_db";

$conn = new mysqli($servername, $username, $password);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
$conn->query("CREATE DATABASE IF NOT EXISTS $dbname");
$conn->select_db($dbname);

$conn->query("CREATE TABLE IF NOT EXISTS ProjectTBL (
    ProjectID INT AUTO_INCREMENT PRIMARY KEY,
    ProjectTitle VARCHAR(100) NOT NULL,
    ProfileImage LONGBLOB,
    BackgroundImage LONGBLOB,
    ProjectDescription TEXT,
    TechStack VARCHAR(200),
    ProjectStatus ENUM('Active','Archived','In Progress') DEFAULT 'Active',
    CreatedTime TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)");

$conn->query("CREATE TABLE IF NOT EXISTS SkillsTBL (
    SkillID INT AUTO_INCREMENT PRIMARY KEY,
    ProjectID INT NOT NULL,
    SkillName VARCHAR(100) NOT NULL,
    Category VARCHAR(100),
    Proficiency ENUM('beginner','intermediate','advanced','expert') DEFAULT 'intermediate',
    CreatedTime TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (ProjectID) REFERENCES ProjectTBL(ProjectID) ON DELETE CASCADE
)");

$conn->query("CREATE TABLE IF NOT EXISTS ExperiencesTBL (
    ExpID INT AUTO_INCREMENT PRIMARY KEY,
    ProjectID INT NOT NULL,
    JobTitle VARCHAR(100),
    Company VARCHAR(100),
    StartDate DATE,
    EndDate DATE,
    FOREIGN KEY (ProjectID) REFERENCES ProjectTBL(ProjectID) ON DELETE CASCADE
)");

$conn->query("CREATE TABLE IF NOT EXISTS contacts (
    ContactID INT AUTO_INCREMENT PRIMARY KEY,
    ContactName VARCHAR(255) NOT NULL,
    ContactEmail VARCHAR(255) NOT NULL,
    ContactSubject VARCHAR(255) NOT NULL,
    ContactMessage TEXT,
    CreatedTime TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)");