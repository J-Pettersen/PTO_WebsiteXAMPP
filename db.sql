CREATE TABLE  employee(
    employee_no int(11),
    first varchar(256) not null,
    last varchar(256) not null,
    position varchar(256) not null,
    shift varchar(256) not null,
    team int(2),
    pwd varcher(256) not null,
    PRIMARY KEY (employee_no)
);


CREATE TABLE  holiday_allowance(
    employee_id int(11),
    tax_year varchar(7) not null,
    allowance int(2) DEFAULT 28,
    booked int(2) DEFAULT 0,
    PRIMARY KEY (employee_id),
    FOREIGN KEY (employee_id) REFERENCES employee (id) ON DELETE CASCADE
);


CREATE TABLE  employee_holiday(
    id int(11) AUTO_INCREMENT,
    employee_id int(11) not null,
    holiday_date DATE not null,
    booked_on DATETIME not null,
    shift ENUM('red','blue','night'),
    accepted TINYINT(1) not null,
    PRIMARY KEY (id),
    FOREIGN KEY (employee_id) REFERENCES employee (id) ON DELETE CASCADE,
    FOREIGN KEY (holiday_date) REFERENCES shift (the_date) ON DELETE CASCADE
);


CREATE TABLE shift(
    the_date DATE,
    morning_avail int(2) DEFAULT 8,
    evening_avail int(2) DEFAULT 8,
    night_avail int(2) DEFAULT 8,
    admin_avail int(2) DEFAULT 8,
    PRIMARY_KEY(the_date)
);