CREATE DATABASE WorkHausDB;
USE WorkHausDB;

CREATE TABLE Customers (
    CustomerID INT AUTO_INCREMENT PRIMARY KEY,
    CustomerUsername VARCHAR(255) NOT NULL,
    CustomerFirstName VARCHAR(255) NOT NULL,
    CustomerLastName VARCHAR(255) NOT NULL,
    CustomerPassword VARCHAR(255) NOT NULL,
    CustomerPhone VARCHAR(255) NOT NULL,
    CustomerEmail VARCHAR(255) NOT NULL,
    CustomerAddress VARCHAR(255) NOT NULL
);

CREATE TABLE WorkSpace (
    WorkspaceID INT PRIMARY KEY AUTO_INCREMENT,
    Description VARCHAR(255),
    Location VARCHAR(100),
    Capacity INT,
    Price DECIMAL(10, 2),
    WorkspaceAvailability ENUM('Yes', 'No'),
    ImageData LONGBLOB
);


CREATE TABLE Booking (
    BookingID INT AUTO_INCREMENT PRIMARY KEY,
    CustomerID INT,
    WorkSpaceID INT,
    Price DECIMAL(10, 2),
    BookingDate DATE,
    StartTime TIME,
    EndTime TIME,
    Additional_Services VARCHAR(255), 
    Total DECIMAL(10, 2),
    FOREIGN KEY (CustomerID) REFERENCES Customers(CustomerID),
    FOREIGN KEY (WorkSpaceID) REFERENCES WorkSpace(WorkSpaceID)
);

CREATE TABLE Staff (
    StaffID INT AUTO_INCREMENT PRIMARY KEY,
    Username VARCHAR(50),
    StaffPassword VARCHAR(28), 
    StaffRole VARCHAR(50)
);

-- sample data
INSERT INTO Customers (CustomerFirstName, CustomerLastName, CustomerPassword, CustomerPhone, CustomerEmail, CustomerAddress, CustomerUsername) 
VALUES ('Ahmed', 'Amin', 'AminoK12!', '05312331318', 'aminoqz12@gmail.com', 'korfez', 'Amin10');

INSERT INTO WorkSpace (Price, WorkspaceAvailability, Description, Location, Capacity)
VALUES
    (50.00, 'Yes', 'A wide and practical workspace.', 'Beylikduzu', 10),
    (40.00, 'No', 'A private workspace for two or one customer.', 'Florya', 2),
    (60.00, 'Yes', ' A workspace for small scale meetings.', 'Esenyurt', 4),
    (70.00, 'Yes', 'A wide and comfort workspace.', 'Vadi Istanbul', 10);
    
INSERT INTO Staff (Username, StaffPassword, StaffRole) VALUES ('admin', 'admin123', 'manager');
INSERT INTO Staff (Username, StaffPassword, StaffRole) 
VALUES  ('vini', 'pass123', 'admin'), ('lunin', 'secure11', 'employee');
