

"C:\Program Files\PostgreSQL\16\bin\pg_dump.exe" --file "D:\xampp\htdocs\collegeprojectaditya.sql" --host "localhost" --port "5432" --username "postgres" --no-password --schema-only --verbose --dbname "college"

--
-- PostgreSQL database dump
--

-- Dumped from database version 16.2
-- Dumped by pg_dump version 16.2

-- Started on 2025-03-01 09:31:01

SET statement_timeout = 0;
SET lock_timeout = 0;
SET idle_in_transaction_session_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SELECT pg_catalog.set_config('search_path', '', false);
SET check_function_bodies = false;
SET xmloption = content;
SET client_min_messages = warning;
SET row_security = off;

--
-- TOC entry 230 (class 1255 OID 17641)
-- Name: delete_sy_user(); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION public.delete_sy_user() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
BEGIN
    DELETE FROM sy_user WHERE user_id = OLD.enrollment;
    RETURN OLD;
END;
$$;


ALTER FUNCTION public.delete_sy_user() OWNER TO postgres;

--
-- TOC entry 243 (class 1255 OID 26011)
-- Name: insert_into_sy_user(); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION public.insert_into_sy_user() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
BEGIN
    CASE TG_OP
        WHEN 'INSERT' THEN
            IF TG_TABLE_NAME = 'student' THEN
                -- Check if user_id already exists
                IF NOT EXISTS (SELECT 1 FROM sy_user WHERE user_id = NEW.enrollment) THEN
                    INSERT INTO sy_user (user_id, user_name, password, user_type)
                    VALUES (NEW.enrollment, NEW.sname, NEW.password, 'Student');
                END IF;

            ELSIF TG_TABLE_NAME = 'teachers' THEN
                -- Check if user_id already exists
                IF NOT EXISTS (SELECT 1 FROM sy_user WHERE user_id = NEW.teacher_id) THEN
                    INSERT INTO sy_user (user_id, user_name, password, user_type)
                    VALUES (NEW.teacher_id, NEW.teacher_name, NEW.password, 'Teacher');
                END IF;

            ELSIF TG_TABLE_NAME = 'admin' THEN
                -- Check if user_id already exists
                IF NOT EXISTS (SELECT 1 FROM sy_user WHERE user_id = NEW.admin_id) THEN
                    INSERT INTO sy_user (user_id, user_name, password, user_type)
                    VALUES (NEW.admin_id, NEW.adm_name, NEW.password, 'Admin');
                END IF;
            END IF;

        WHEN 'UPDATE' THEN
            IF TG_TABLE_NAME = 'student' THEN
                UPDATE sy_user
                SET user_name = NEW.sname, password = NEW.password
                WHERE user_id = NEW.enrollment;
            
            ELSIF TG_TABLE_NAME = 'teachers' THEN
                UPDATE sy_user
                SET user_name = NEW.teacher_name, password = NEW.password
                WHERE user_id = NEW.teacher_id;
            
            ELSIF TG_TABLE_NAME = 'admin' THEN
                UPDATE sy_user
                SET user_name = NEW.adm_name, password = NEW.password
                WHERE user_id = NEW.admin_id;
            END IF;
            
        WHEN 'DELETE' THEN
            IF TG_TABLE_NAME = 'student' THEN
                DELETE FROM sy_user WHERE user_id = OLD.enrollment;
            
            ELSIF TG_TABLE_NAME = 'teachers' THEN
                DELETE FROM sy_user WHERE user_id = OLD.teacher_id;
            
            ELSIF TG_TABLE_NAME = 'admin' THEN
                DELETE FROM sy_user WHERE user_id = OLD.admin_id;
            END IF;
    END CASE;

    -- Return new or old value based on operation type
    IF TG_OP = 'DELETE' THEN
        RETURN OLD;
    ELSE
        RETURN NEW;
    END IF;
END;
$$;


ALTER FUNCTION public.insert_into_sy_user() OWNER TO postgres;

--
-- TOC entry 229 (class 1255 OID 17350)
-- Name: insertcd(); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION public.insertcd() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
BEGIN
    UPDATE student
    SET 
        course_code = (SELECT course_code FROM course WHERE course_name = NEW.course_name),
        department_code = (SELECT dept_code FROM course WHERE course_name = NEW.course_name),
        department_name = (SELECT department_name FROM departments WHERE department_code = (SELECT dept_code FROM course WHERE course_name = NEW.course_name))
    WHERE enrollment = NEW.enrollment;

    RETURN NEW;
END;
$$;


ALTER FUNCTION public.insertcd() OWNER TO postgres;

--
-- TOC entry 235 (class 1255 OID 25429)
-- Name: insertcdtemp(); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION public.insertcdtemp() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
BEGIN
    UPDATE tempstudent
    SET 
        course_code = (SELECT course_code FROM course WHERE course_name = NEW.course_name),
        department_code = (SELECT dept_code FROM course WHERE course_name = NEW.course_name),
        department_name = (SELECT department_name FROM departments WHERE department_code = (SELECT dept_code FROM course WHERE course_name = NEW.course_name))
    WHERE enrollment = NEW.enrollment;

    RETURN NEW;
END;
$$;


ALTER FUNCTION public.insertcdtemp() OWNER TO postgres;

--
-- TOC entry 228 (class 1255 OID 28317)
-- Name: update_year_based_on_semester(); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION public.update_year_based_on_semester() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
BEGIN
    NEW.year := 
        CASE NEW.semester
            WHEN 'Semester 1' THEN 1
            WHEN 'Semester 2' THEN 1
            WHEN 'Semester 3' THEN 2
            WHEN 'Semester 4' THEN 2
            WHEN 'Semester 5' THEN 3
            WHEN 'Semester 6' THEN 3
            WHEN 'Semester 7' THEN 4
            WHEN 'Semester 8' THEN 4
            ELSE NULL -- Handle cases where input doesn't match any pattern
        END;
    RETURN NEW;
END;
$$;


ALTER FUNCTION public.update_year_based_on_semester() OWNER TO postgres;

SET default_tablespace = '';

SET default_table_access_method = heap;

--
-- TOC entry 218 (class 1259 OID 25245)
-- Name: admin; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.admin (
    admin_id character varying(34) NOT NULL,
    adm_name character varying(54),
    password character varying(34),
    contact_info character varying(34),
    department_code character varying(34),
    create_at timestamp without time zone DEFAULT CURRENT_TIMESTAMP
);


ALTER TABLE public.admin OWNER TO postgres;

--
-- TOC entry 216 (class 1259 OID 16993)
-- Name: course; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.course (
    course_code character varying(230) NOT NULL,
    course_name character varying(255) NOT NULL,
    description text,
    duration character varying(100),
    dept_code character varying(233),
    created_at timestamp without time zone DEFAULT CURRENT_TIMESTAMP,
    course_coordinator character varying(255)
);


ALTER TABLE public.course OWNER TO postgres;

--
-- TOC entry 223 (class 1259 OID 26929)
-- Name: course_teacher; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.course_teacher (
    course_code character varying(230) NOT NULL,
    teacher_id character varying(20) NOT NULL,
    assigned_at timestamp without time zone DEFAULT CURRENT_TIMESTAMP
);


ALTER TABLE public.course_teacher OWNER TO postgres;

--
-- TOC entry 215 (class 1259 OID 16978)
-- Name: departments; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.departments (
    department_code character varying(230) NOT NULL,
    department_name character varying(255) NOT NULL,
    hod_name character varying(255) NOT NULL,
    description text NOT NULL,
    location character varying(255) NOT NULL,
    contact character varying(255) NOT NULL,
    created_at timestamp without time zone DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT departments_location CHECK ((location IS NOT NULL))
);


ALTER TABLE public.departments OWNER TO postgres;

--
-- TOC entry 227 (class 1259 OID 30423)
-- Name: messages; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.messages (
    msg_id integer NOT NULL,
    sender_type character varying(10),
    sender_id character varying(20) NOT NULL,
    course_code character varying(20),
    semester character varying(10) NOT NULL,
    message text NOT NULL,
    message_date date NOT NULL,
    target_type character varying(300),
    sent_at timestamp without time zone DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT messages_sender_type_check CHECK (((sender_type)::text = ANY ((ARRAY['teacher'::character varying, 'admin'::character varying])::text[])))
);


ALTER TABLE public.messages OWNER TO postgres;

--
-- TOC entry 226 (class 1259 OID 30422)
-- Name: messages_msg_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.messages_msg_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.messages_msg_id_seq OWNER TO postgres;

--
-- TOC entry 4955 (class 0 OID 0)
-- Dependencies: 226
-- Name: messages_msg_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.messages_msg_id_seq OWNED BY public.messages.msg_id;


--
-- TOC entry 225 (class 1259 OID 30349)
-- Name: notes; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.notes (
    notes_id integer NOT NULL,
    course_code character varying,
    teacher_id character varying,
    year integer NOT NULL,
    file_path character varying,
    upload_data timestamp without time zone DEFAULT CURRENT_TIMESTAMP
);


ALTER TABLE public.notes OWNER TO postgres;

--
-- TOC entry 224 (class 1259 OID 30348)
-- Name: notes_notes_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.notes_notes_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.notes_notes_id_seq OWNER TO postgres;

--
-- TOC entry 4956 (class 0 OID 0)
-- Dependencies: 224
-- Name: notes_notes_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.notes_notes_id_seq OWNED BY public.notes.notes_id;


--
-- TOC entry 219 (class 1259 OID 25467)
-- Name: student; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.student (
    enrollment character varying(34) NOT NULL,
    sname character varying(34),
    gender character varying(10),
    password character varying(255),
    parent_name character varying(344),
    email character varying(34),
    phone bigint,
    address character varying(255),
    course_name character varying(255),
    semester character varying(34),
    dob date,
    acdemic_year character varying(90),
    course_code character varying(230),
    department_code character varying(230),
    department_name character varying(255),
    feerecipt character varying(255),
    studentphoto character varying(255),
    "time" timestamp without time zone DEFAULT CURRENT_TIMESTAMP,
    year integer,
    CONSTRAINT student_gender_check CHECK (((gender)::text = ANY (ARRAY[('Male'::character varying)::text, ('Female'::character varying)::text])))
);


ALTER TABLE public.student OWNER TO postgres;

--
-- TOC entry 217 (class 1259 OID 17606)
-- Name: sy_user; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.sy_user (
    user_id character varying(34),
    user_name character varying(34),
    password character varying(255),
    user_type character varying(15),
    create_at timestamp without time zone DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT sy_user_user_type_check CHECK (((user_type)::text = ANY ((ARRAY['Student'::character varying, 'Teacher'::character varying, 'Admin'::character varying])::text[])))
);


ALTER TABLE public.sy_user OWNER TO postgres;

--
-- TOC entry 222 (class 1259 OID 25565)
-- Name: teachers; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.teachers (
    teacher_id character varying(255) NOT NULL,
    teacher_name character varying(255) NOT NULL,
    email character varying(255) NOT NULL,
    password character varying(255) NOT NULL,
    department character varying(255),
    phone bigint,
    address character varying(255),
    "time" timestamp without time zone DEFAULT CURRENT_TIMESTAMP,
    qualification character varying(255)
);


ALTER TABLE public.teachers OWNER TO postgres;

--
-- TOC entry 4957 (class 0 OID 0)
-- Dependencies: 222
-- Name: COLUMN teachers.qualification; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.teachers.qualification IS 'Qualification of the teacher';


--
-- TOC entry 221 (class 1259 OID 25564)
-- Name: teachers_teacher_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.teachers_teacher_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.teachers_teacher_id_seq OWNER TO postgres;

--
-- TOC entry 4958 (class 0 OID 0)
-- Dependencies: 221
-- Name: teachers_teacher_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.teachers_teacher_id_seq OWNED BY public.teachers.teacher_id;


--
-- TOC entry 220 (class 1259 OID 25501)
-- Name: tempstudent; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.tempstudent (
    enrollment character varying(34) NOT NULL,
    sname character varying(34),
    gender character varying(10),
    password character varying(255),
    parent_name character varying(344),
    email character varying(34),
    phone bigint,
    address character varying(255),
    course_name character varying(255),
    semester character varying(34),
    dob date,
    acdemic_year character varying(90),
    course_code character varying(230),
    department_code character varying(230),
    department_name character varying(255),
    feerecipt character varying(255),
    studentphoto character varying(255),
    "time" timestamp without time zone DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT student_gender_check CHECK (((gender)::text = ANY (ARRAY[('Male'::character varying)::text, ('Female'::character varying)::text])))
);


ALTER TABLE public.tempstudent OWNER TO postgres;

--
-- TOC entry 4742 (class 2604 OID 30426)
-- Name: messages msg_id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.messages ALTER COLUMN msg_id SET DEFAULT nextval('public.messages_msg_id_seq'::regclass);


--
-- TOC entry 4740 (class 2604 OID 30352)
-- Name: notes notes_id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.notes ALTER COLUMN notes_id SET DEFAULT nextval('public.notes_notes_id_seq'::regclass);


--
-- TOC entry 4737 (class 2604 OID 26077)
-- Name: teachers teacher_id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.teachers ALTER COLUMN teacher_id SET DEFAULT nextval('public.teachers_teacher_id_seq'::regclass);


--
-- TOC entry 4764 (class 2606 OID 25251)
-- Name: admin admin_adm_name_key; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.admin
    ADD CONSTRAINT admin_adm_name_key UNIQUE (adm_name);


--
-- TOC entry 4766 (class 2606 OID 25249)
-- Name: admin admin_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.admin
    ADD CONSTRAINT admin_pkey PRIMARY KEY (admin_id);


--
-- TOC entry 4758 (class 2606 OID 17001)
-- Name: course course_course_name_key; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.course
    ADD CONSTRAINT course_course_name_key UNIQUE (course_name);


--
-- TOC entry 4760 (class 2606 OID 16999)
-- Name: course course_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.course
    ADD CONSTRAINT course_pkey PRIMARY KEY (course_code);


--
-- TOC entry 4778 (class 2606 OID 28292)
-- Name: course_teacher course_teacher_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.course_teacher
    ADD CONSTRAINT course_teacher_pkey PRIMARY KEY (course_code, teacher_id);


--
-- TOC entry 4750 (class 2606 OID 16992)
-- Name: departments departments_contact_key; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.departments
    ADD CONSTRAINT departments_contact_key UNIQUE (contact);


--
-- TOC entry 4752 (class 2606 OID 16986)
-- Name: departments departments_department_name_key; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.departments
    ADD CONSTRAINT departments_department_name_key UNIQUE (department_name);


--
-- TOC entry 4754 (class 2606 OID 16988)
-- Name: departments departments_hod_name_key; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.departments
    ADD CONSTRAINT departments_hod_name_key UNIQUE (hod_name);


--
-- TOC entry 4756 (class 2606 OID 16984)
-- Name: departments departments_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.departments
    ADD CONSTRAINT departments_pkey PRIMARY KEY (department_code);


--
-- TOC entry 4782 (class 2606 OID 30432)
-- Name: messages messages_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.messages
    ADD CONSTRAINT messages_pkey PRIMARY KEY (msg_id);


--
-- TOC entry 4780 (class 2606 OID 30357)
-- Name: notes notes_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.notes
    ADD CONSTRAINT notes_pkey PRIMARY KEY (notes_id);


--
-- TOC entry 4770 (class 2606 OID 25475)
-- Name: student student_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.student
    ADD CONSTRAINT student_pkey PRIMARY KEY (enrollment);


--
-- TOC entry 4762 (class 2606 OID 17611)
-- Name: sy_user sy_user_user_id_key; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.sy_user
    ADD CONSTRAINT sy_user_user_id_key UNIQUE (user_id);


--
-- TOC entry 4774 (class 2606 OID 25575)
-- Name: teachers teachers_email_key; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.teachers
    ADD CONSTRAINT teachers_email_key UNIQUE (email);


--
-- TOC entry 4776 (class 2606 OID 26079)
-- Name: teachers teachers_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.teachers
    ADD CONSTRAINT teachers_pkey PRIMARY KEY (teacher_id);


--
-- TOC entry 4772 (class 2606 OID 25509)
-- Name: tempstudent temp_student_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tempstudent
    ADD CONSTRAINT temp_student_pkey PRIMARY KEY (enrollment);


--
-- TOC entry 4768 (class 2606 OID 25260)
-- Name: admin unique_contact_info; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.admin
    ADD CONSTRAINT unique_contact_info UNIQUE (contact_info);


--
-- TOC entry 4797 (class 2620 OID 27376)
-- Name: admin admin_sy_user_trigger; Type: TRIGGER; Schema: public; Owner: postgres
--

CREATE TRIGGER admin_sy_user_trigger AFTER INSERT OR DELETE OR UPDATE ON public.admin FOR EACH ROW EXECUTE FUNCTION public.insert_into_sy_user();


--
-- TOC entry 4799 (class 2620 OID 25491)
-- Name: student delete_sy_user_trigger; Type: TRIGGER; Schema: public; Owner: postgres
--

CREATE TRIGGER delete_sy_user_trigger AFTER DELETE ON public.student FOR EACH ROW EXECUTE FUNCTION public.delete_sy_user();


--
-- TOC entry 4798 (class 2620 OID 26015)
-- Name: admin insert_sy_user_after_admin_insert; Type: TRIGGER; Schema: public; Owner: postgres
--

CREATE TRIGGER insert_sy_user_after_admin_insert AFTER INSERT ON public.admin FOR EACH ROW EXECUTE FUNCTION public.insert_into_sy_user();


--
-- TOC entry 4800 (class 2620 OID 26013)
-- Name: student insert_sy_user_after_student_insert; Type: TRIGGER; Schema: public; Owner: postgres
--

CREATE TRIGGER insert_sy_user_after_student_insert AFTER INSERT ON public.student FOR EACH ROW EXECUTE FUNCTION public.insert_into_sy_user();


--
-- TOC entry 4805 (class 2620 OID 26014)
-- Name: teachers insert_sy_user_after_teacher_insert; Type: TRIGGER; Schema: public; Owner: postgres
--

CREATE TRIGGER insert_sy_user_after_teacher_insert AFTER INSERT OR UPDATE ON public.teachers FOR EACH ROW EXECUTE FUNCTION public.insert_into_sy_user();


--
-- TOC entry 4801 (class 2620 OID 25492)
-- Name: student insertcd1; Type: TRIGGER; Schema: public; Owner: postgres
--

CREATE TRIGGER insertcd1 AFTER INSERT ON public.student FOR EACH ROW EXECUTE FUNCTION public.insertcd();


--
-- TOC entry 4802 (class 2620 OID 27374)
-- Name: student student_sy_user_trigger; Type: TRIGGER; Schema: public; Owner: postgres
--

CREATE TRIGGER student_sy_user_trigger AFTER INSERT OR DELETE OR UPDATE ON public.student FOR EACH ROW EXECUTE FUNCTION public.insert_into_sy_user();


--
-- TOC entry 4806 (class 2620 OID 27375)
-- Name: teachers teachers_sy_user_trigger; Type: TRIGGER; Schema: public; Owner: postgres
--

CREATE TRIGGER teachers_sy_user_trigger AFTER INSERT OR DELETE OR UPDATE ON public.teachers FOR EACH ROW EXECUTE FUNCTION public.insert_into_sy_user();


--
-- TOC entry 4804 (class 2620 OID 25525)
-- Name: tempstudent tempinsertcd1; Type: TRIGGER; Schema: public; Owner: postgres
--

CREATE TRIGGER tempinsertcd1 AFTER INSERT ON public.tempstudent FOR EACH ROW EXECUTE FUNCTION public.insertcdtemp();


--
-- TOC entry 4803 (class 2620 OID 28319)
-- Name: student trg_update_year; Type: TRIGGER; Schema: public; Owner: postgres
--

CREATE TRIGGER trg_update_year BEFORE INSERT OR UPDATE ON public.student FOR EACH ROW EXECUTE FUNCTION public.update_year_based_on_semester();


--
-- TOC entry 4784 (class 2606 OID 25252)
-- Name: admin admin_department_code_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.admin
    ADD CONSTRAINT admin_department_code_fkey FOREIGN KEY (department_code) REFERENCES public.departments(department_code);


--
-- TOC entry 4783 (class 2606 OID 17002)
-- Name: course course_dept_code_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.course
    ADD CONSTRAINT course_dept_code_fkey FOREIGN KEY (dept_code) REFERENCES public.departments(department_code) ON DELETE CASCADE;


--
-- TOC entry 4792 (class 2606 OID 26935)
-- Name: course_teacher course_teacher_course_code_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.course_teacher
    ADD CONSTRAINT course_teacher_course_code_fkey FOREIGN KEY (course_code) REFERENCES public.course(course_code) ON DELETE CASCADE;


--
-- TOC entry 4793 (class 2606 OID 26940)
-- Name: course_teacher course_teacher_teacher_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.course_teacher
    ADD CONSTRAINT course_teacher_teacher_id_fkey FOREIGN KEY (teacher_id) REFERENCES public.teachers(teacher_id) ON DELETE CASCADE;


--
-- TOC entry 4791 (class 2606 OID 26063)
-- Name: teachers department; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.teachers
    ADD CONSTRAINT department FOREIGN KEY (department) REFERENCES public.departments(department_code);


--
-- TOC entry 4796 (class 2606 OID 30433)
-- Name: messages messages_course_code_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.messages
    ADD CONSTRAINT messages_course_code_fkey FOREIGN KEY (course_code) REFERENCES public.course(course_code) ON DELETE CASCADE;


--
-- TOC entry 4794 (class 2606 OID 30358)
-- Name: notes notes_course_code_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.notes
    ADD CONSTRAINT notes_course_code_fkey FOREIGN KEY (course_code) REFERENCES public.course(course_code);


--
-- TOC entry 4795 (class 2606 OID 30363)
-- Name: notes notes_teacher_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.notes
    ADD CONSTRAINT notes_teacher_id_fkey FOREIGN KEY (teacher_id) REFERENCES public.teachers(teacher_id);


--
-- TOC entry 4785 (class 2606 OID 25476)
-- Name: student student_course_code_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.student
    ADD CONSTRAINT student_course_code_fkey FOREIGN KEY (course_code) REFERENCES public.course(course_code);


--
-- TOC entry 4788 (class 2606 OID 25510)
-- Name: tempstudent student_course_code_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tempstudent
    ADD CONSTRAINT student_course_code_fkey FOREIGN KEY (course_code) REFERENCES public.course(course_code);


--
-- TOC entry 4786 (class 2606 OID 25481)
-- Name: student student_department_code_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.student
    ADD CONSTRAINT student_department_code_fkey FOREIGN KEY (department_code) REFERENCES public.departments(department_code);


--
-- TOC entry 4789 (class 2606 OID 25515)
-- Name: tempstudent student_department_code_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tempstudent
    ADD CONSTRAINT student_department_code_fkey FOREIGN KEY (department_code) REFERENCES public.departments(department_code);


--
-- TOC entry 4787 (class 2606 OID 25486)
-- Name: student student_department_name_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.student
    ADD CONSTRAINT student_department_name_fkey FOREIGN KEY (department_name) REFERENCES public.departments(department_name);


--
-- TOC entry 4790 (class 2606 OID 25520)
-- Name: tempstudent student_department_name_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tempstudent
    ADD CONSTRAINT student_department_name_fkey FOREIGN KEY (department_name) REFERENCES public.departments(department_name);


-- Completed on 2025-03-01 09:31:02

--
-- PostgreSQL database dump complete
--

