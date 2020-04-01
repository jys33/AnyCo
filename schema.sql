--Creating the Equipment Table
--In SQL*Plus connect as the HR user and run the following script:
sqlplus hr/welcome@localhost
CREATE TABLE equipment(
    id          NUMBER PRIMARY KEY,
    employee_id REFERENCES employees(employee_id) ON DELETE CASCADE,
    equip_name  VARCHAR2(20) NOT NULL);
 
CREATE SEQUENCE equipment_seq;
CREATE TRIGGER equipment_trig BEFORE INSERT ON equipment FOR EACH ROW
BEGIN
    :NEW.id := equipment_seq.NEXTVAL;
END;
/
-- Sample Data
INSERT INTO equipment (employee_id, equip_name) VALUES (100, 'pen');
INSERT INTO equipment (employee_id, equip_name) VALUES (100, 'telephone');
INSERT INTO equipment (employee_id, equip_name) VALUES (101, 'pen');
INSERT INTO equipment (employee_id, equip_name) VALUES (101, 'paper');
INSERT INTO equipment (employee_id, equip_name) VALUES (101, 'car');
INSERT INTO equipment (employee_id, equip_name) VALUES (102, 'pen');
INSERT INTO equipment (employee_id, equip_name) VALUES (102, 'paper');
INSERT INTO equipment (employee_id, equip_name) VALUES (102, 'telephone');
INSERT INTO equipment (employee_id, equip_name) VALUES (103, 'telephone');
INSERT INTO equipment (employee_id, equip_name) VALUES (103, 'computer');
INSERT INTO equipment (employee_id, equip_name) VALUES (121, 'computer');
INSERT INTO equipment (employee_id, equip_name) VALUES (180, 'pen');
INSERT INTO equipment (employee_id, equip_name) VALUES (180, 'paper');
INSERT INTO equipment (employee_id, equip_name) VALUES (180, 'cardboard box');
COMMIT;

--In SQL*Plus create a procedure as HR:

CREATE OR REPLACE PROCEDURE get_equip(eid_p IN NUMBER, RC OUT SYS_REFCURSOR) AS
BEGIN
    OPEN rc FOR SELECT   equip_name
                FROM     equipment
                WHERE    employee_id = eid_p
                ORDER BY equip_name;
END;
/

--In PHP this procedure can be called by running an anonymous PL/SQL block:

BEGIN get_equip(:id, :rc); END;

CREATE OR REPLACE PACKAGE equip_pkg AS
    TYPE arrtype IS TABLE OF VARCHAR2(20) INDEX BY PLS_INTEGER;
    PROCEDURE insert_equip(eid_p IN NUMBER, eqa_p IN arrtype);
END equip_pkg;
/

CREATE OR REPLACE PACKAGE BODY equip_pkg AS
    PROCEDURE insert_equip(eid_p IN NUMBER, eqa_p IN arrtype) IS
    BEGIN
        FORALL i IN INDICES OF eqa_p
            INSERT INTO equipment (employee_id, equip_name) 
                                   VALUES (eid_p, eqa_p(i));
    END insert_equip;
END equip_pkg;
/

CREATE TABLE pictures (id NUMBER, pic BLOB);
 
CREATE SEQUENCE pictures_seq;
CREATE TRIGGER pictures_trig BEFORE INSERT ON pictures FOR EACH ROW
BEGIN
    :NEW.id := pictures_seq.NEXTVAL;
END;
/