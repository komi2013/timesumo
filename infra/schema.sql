--
-- PostgreSQL database dump
--

-- Dumped from database version 11.7
-- Dumped by pg_dump version 11.7

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
-- Name: approved_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.approved_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    MAXVALUE 99999999
    CACHE 1;


ALTER TABLE public.approved_id_seq OWNER TO postgres;

--
-- Name: book_id; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.book_id
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.book_id OWNER TO postgres;

SET default_tablespace = '';

SET default_with_oids = false;

--
-- Name: c_area; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.c_area (
    area_id integer NOT NULL,
    area_name character varying(50) DEFAULT ''::character varying NOT NULL,
    updated_at timestamp without time zone DEFAULT now() NOT NULL,
    group_id integer DEFAULT 0 NOT NULL,
    usr_id integer DEFAULT 0 NOT NULL
);


ALTER TABLE public.c_area OWNER TO postgres;

--
-- Name: COLUMN c_area.usr_id; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.c_area.usr_id IS 'after register initial data';


--
-- Name: c_area_area_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.c_area_area_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.c_area_area_id_seq OWNER TO postgres;

--
-- Name: c_area_area_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.c_area_area_id_seq OWNED BY public.c_area.area_id;


--
-- Name: c_holiday; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.c_holiday (
    holiday_id integer NOT NULL,
    holiday_name text DEFAULT ''::text NOT NULL,
    holiday_date date DEFAULT now() NOT NULL,
    country text DEFAULT ''::text NOT NULL
);


ALTER TABLE public.c_holiday OWNER TO postgres;

--
-- Name: c_holiday_holiday_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.c_holiday_holiday_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.c_holiday_holiday_id_seq OWNER TO postgres;

--
-- Name: c_holiday_holiday_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.c_holiday_holiday_id_seq OWNED BY public.c_holiday.holiday_id;


--
-- Name: c_link; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.c_link (
    link_id integer NOT NULL,
    url text DEFAULT ''::text NOT NULL,
    ja text DEFAULT ''::text NOT NULL,
    en text DEFAULT ''::text NOT NULL,
    priority integer DEFAULT 0 NOT NULL,
    updated_at timestamp without time zone DEFAULT now() NOT NULL,
    group_owner smallint DEFAULT '0'::smallint NOT NULL,
    approver smallint DEFAULT '0'::smallint NOT NULL,
    public smallint DEFAULT '0'::smallint NOT NULL
);


ALTER TABLE public.c_link OWNER TO postgres;

--
-- Name: c_link_link_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.c_link_link_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.c_link_link_id_seq OWNER TO postgres;

--
-- Name: c_link_link_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.c_link_link_id_seq OWNED BY public.c_link.link_id;


--
-- Name: h_compensatory; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.h_compensatory (
    compensatory_id integer DEFAULT 0 NOT NULL,
    compensatory_start date DEFAULT now() NOT NULL,
    compensatory_end date DEFAULT now() NOT NULL,
    usr_id integer DEFAULT 0 NOT NULL,
    group_id integer DEFAULT 0 NOT NULL,
    schedule_id integer DEFAULT 0 NOT NULL,
    updated_at timestamp without time zone DEFAULT now() NOT NULL,
    action_by integer DEFAULT 0 NOT NULL,
    action_at timestamp without time zone DEFAULT now() NOT NULL,
    action_flg integer DEFAULT 0 NOT NULL,
    original_by text DEFAULT ''::text NOT NULL,
    compensatory_hours integer DEFAULT 0 NOT NULL,
    compensatory_days integer DEFAULT 0 NOT NULL
);


ALTER TABLE public.h_compensatory OWNER TO postgres;

--
-- Name: h_extra; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.h_extra (
    extra_id integer DEFAULT 0 NOT NULL,
    extra_start time without time zone,
    extra_end time without time zone,
    dayoff_flg integer DEFAULT 0 NOT NULL,
    usr_id integer DEFAULT 0 NOT NULL,
    group_id integer DEFAULT 0 NOT NULL,
    extra_ratio real DEFAULT '1'::real NOT NULL,
    over_flg integer DEFAULT 0 NOT NULL,
    hour_start integer DEFAULT 0 NOT NULL,
    hour_end integer DEFAULT 0 NOT NULL,
    updated_at timestamp without time zone DEFAULT now() NOT NULL,
    action_by integer DEFAULT 0 NOT NULL,
    action_at timestamp without time zone DEFAULT now() NOT NULL,
    action_flg integer DEFAULT 0 NOT NULL,
    original_by text DEFAULT ''::text NOT NULL
);


ALTER TABLE public.h_extra OWNER TO postgres;

--
-- Name: h_facility; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.h_facility (
    facility_id integer DEFAULT 0 NOT NULL,
    facility_name text DEFAULT ''::text NOT NULL,
    updated_at timestamp without time zone DEFAULT now() NOT NULL,
    amount integer DEFAULT 0 NOT NULL,
    group_id integer DEFAULT 0 NOT NULL,
    action_by integer DEFAULT 0 NOT NULL,
    action_at timestamp without time zone DEFAULT now() NOT NULL,
    action_flg smallint DEFAULT '0'::smallint NOT NULL
);


ALTER TABLE public.h_facility OWNER TO postgres;

--
-- Name: COLUMN h_facility.facility_id; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.h_facility.facility_id IS 'usr_id';


--
-- Name: COLUMN h_facility.action_flg; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.h_facility.action_flg IS '0=delete,1=update';


--
-- Name: h_group; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.h_group (
    group_id integer DEFAULT 0 NOT NULL,
    group_name text DEFAULT ''::text NOT NULL,
    updated_at timestamp without time zone DEFAULT now() NOT NULL,
    password text DEFAULT ''::text NOT NULL,
    open_time time without time zone DEFAULT '00:00:00'::time without time zone NOT NULL,
    close_time time without time zone DEFAULT '00:00:00'::time without time zone NOT NULL,
    action_by integer DEFAULT 0 NOT NULL,
    action_at timestamp without time zone DEFAULT now() NOT NULL,
    action_flg smallint DEFAULT '0'::smallint NOT NULL,
    area_id json DEFAULT '[]'::json NOT NULL,
    token text DEFAULT ''::text NOT NULL
);


ALTER TABLE public.h_group OWNER TO postgres;

--
-- Name: h_group_relate; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.h_group_relate (
    group_relate_id integer DEFAULT 0 NOT NULL,
    group_id integer DEFAULT 0 NOT NULL,
    usr_id integer DEFAULT 0 NOT NULL,
    priority integer DEFAULT 0 NOT NULL,
    updated_at timestamp without time zone DEFAULT now() NOT NULL,
    owner_flg integer DEFAULT 0 NOT NULL,
    action_by integer DEFAULT 0 NOT NULL,
    action_at timestamp without time zone DEFAULT now() NOT NULL,
    action_flg smallint DEFAULT '0'::smallint NOT NULL
);


ALTER TABLE public.h_group_relate OWNER TO postgres;

--
-- Name: COLUMN h_group_relate.action_flg; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.h_group_relate.action_flg IS '0=delete, 1=update';


--
-- Name: h_leave_amount; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.h_leave_amount (
    usr_id integer DEFAULT 0 NOT NULL,
    enable_start date DEFAULT now() NOT NULL,
    enable_end date DEFAULT now() NOT NULL,
    grant_days integer DEFAULT 0 NOT NULL,
    used_days integer DEFAULT 0 NOT NULL,
    note text DEFAULT ''::text NOT NULL,
    updated_at timestamp without time zone DEFAULT now() NOT NULL,
    leave_id integer DEFAULT 0 NOT NULL,
    action_by integer DEFAULT 0 NOT NULL,
    action_at timestamp without time zone DEFAULT now() NOT NULL,
    action_flg integer DEFAULT 0 NOT NULL,
    original_by text DEFAULT ''::text NOT NULL,
    schedule_id smallint DEFAULT '0'::smallint NOT NULL
);


ALTER TABLE public.h_leave_amount OWNER TO postgres;

--
-- Name: h_routine; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.h_routine (
    routine_id integer DEFAULT 0 NOT NULL,
    usr_id integer DEFAULT 0 NOT NULL,
    start_0 time without time zone,
    end_0 time without time zone,
    start_1 time without time zone,
    end_1 time without time zone,
    start_2 time without time zone,
    end_2 time without time zone,
    start_3 time without time zone,
    end_3 time without time zone,
    start_4 time without time zone,
    end_4 time without time zone,
    start_5 time without time zone,
    end_5 time without time zone,
    start_6 time without time zone,
    end_6 time without time zone,
    group_id integer DEFAULT 0 NOT NULL,
    action_by integer DEFAULT 0 NOT NULL,
    action_flg integer DEFAULT 0 NOT NULL,
    action_at timestamp without time zone DEFAULT now() NOT NULL,
    updated_at timestamp without time zone DEFAULT now() NOT NULL,
    fix_flg smallint DEFAULT '0'::smallint NOT NULL
);


ALTER TABLE public.h_routine OWNER TO postgres;

--
-- Name: h_rule; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.h_rule (
    usr_id integer DEFAULT 0 NOT NULL,
    group_id integer DEFAULT 0 NOT NULL,
    holiday_flg smallint DEFAULT '0'::smallint NOT NULL,
    approver1 integer DEFAULT 0 NOT NULL,
    approver2 integer DEFAULT 0 NOT NULL,
    compensatory_within smallint DEFAULT '0'::smallint NOT NULL,
    minimum_break smallint DEFAULT '0'::smallint NOT NULL,
    break_minute smallint DEFAULT '0'::smallint NOT NULL,
    wage real DEFAULT '0'::real NOT NULL,
    currency smallint DEFAULT '0'::smallint NOT NULL,
    updated_at timestamp without time zone DEFAULT now() NOT NULL,
    action_by integer DEFAULT 0 NOT NULL,
    action_at timestamp without time zone DEFAULT now() NOT NULL,
    action_flg smallint DEFAULT '0'::smallint NOT NULL,
    compensatory_flg smallint DEFAULT '0'::smallint NOT NULL
);


ALTER TABLE public.h_rule OWNER TO postgres;

--
-- Name: COLUMN h_rule.action_flg; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.h_rule.action_flg IS '0=delete, 1=update';


--
-- Name: h_schedule; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.h_schedule (
    schedule_id integer DEFAULT 0 NOT NULL,
    time_start timestamp without time zone DEFAULT now() NOT NULL,
    time_end timestamp without time zone DEFAULT now() NOT NULL,
    title character varying(20) DEFAULT ''::character varying NOT NULL,
    usr_id integer DEFAULT 0 NOT NULL,
    tag integer DEFAULT 0 NOT NULL,
    group_id integer DEFAULT 0 NOT NULL,
    public_title character varying(20) DEFAULT ''::character varying NOT NULL,
    public_tag integer DEFAULT 0 NOT NULL,
    updated_at timestamp without time zone DEFAULT now() NOT NULL,
    access_right integer DEFAULT 1 NOT NULL,
    action_by integer DEFAULT 0 NOT NULL,
    action_at timestamp without time zone DEFAULT now() NOT NULL,
    action_flg integer DEFAULT 0 NOT NULL,
    original_by text DEFAULT ''::text NOT NULL,
    usr_id_json json
);


ALTER TABLE public.h_schedule OWNER TO postgres;

--
-- Name: COLUMN h_schedule.usr_id; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.h_schedule.usr_id IS 'facility_id too';


--
-- Name: COLUMN h_schedule.tag; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.h_schedule.tag IS '1=meeting, 2=off, 3=out, 4=task, 5=shift, 6=service, 7=facility';


--
-- Name: COLUMN h_schedule.group_id; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.h_schedule.group_id IS '0 = todo only, 1 = all, 2 ~ group_type_id';


--
-- Name: COLUMN h_schedule.action_by; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.h_schedule.action_by IS 'usr_id';


--
-- Name: COLUMN h_schedule.action_flg; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.h_schedule.action_flg IS '0=delete, 1=update';


--
-- Name: h_timestamp; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.h_timestamp (
    timestamp_id integer DEFAULT 0 NOT NULL,
    time_in timestamp without time zone DEFAULT now() NOT NULL,
    time_out timestamp without time zone DEFAULT now() NOT NULL,
    usr_id integer DEFAULT 0 NOT NULL,
    longitude double precision DEFAULT '0'::double precision NOT NULL,
    latitude double precision DEFAULT '0'::double precision NOT NULL,
    group_id integer DEFAULT 0 NOT NULL,
    manual_flg integer DEFAULT 0 NOT NULL,
    action_by integer DEFAULT 0 NOT NULL,
    action_at timestamp without time zone DEFAULT now() NOT NULL,
    action_flg integer DEFAULT 0 NOT NULL,
    approved_id integer DEFAULT 0 NOT NULL,
    private_ip inet DEFAULT '0.0.0.0'::inet NOT NULL,
    public_ip inet DEFAULT '0.0.0.0'::inet NOT NULL,
    break_amount integer DEFAULT 0 NOT NULL,
    offday smallint DEFAULT '0'::smallint NOT NULL,
    overwork integer DEFAULT 0 NOT NULL,
    offmin integer DEFAULT 0 NOT NULL,
    overtime json,
    routine_start time without time zone DEFAULT '00:00:00'::time without time zone NOT NULL,
    routine_end time without time zone DEFAULT '00:00:00'::time without time zone NOT NULL,
    schedules json
);


ALTER TABLE public.h_timestamp OWNER TO postgres;

--
-- Name: h_todo; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.h_todo (
    todo_id integer DEFAULT 0 NOT NULL,
    todo text DEFAULT ''::text NOT NULL,
    file_paths json,
    updated_at timestamp without time zone DEFAULT now() NOT NULL,
    schedule_id integer DEFAULT 0 NOT NULL,
    usr_id integer DEFAULT 0 NOT NULL,
    action_by integer DEFAULT 0 NOT NULL,
    action_at timestamp without time zone DEFAULT now() NOT NULL,
    action_flg integer DEFAULT 0 NOT NULL
);


ALTER TABLE public.h_todo OWNER TO postgres;

--
-- Name: COLUMN h_todo.action_by; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.h_todo.action_by IS 'usr_id';


--
-- Name: COLUMN h_todo.action_flg; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.h_todo.action_flg IS '0=delete, 1=update';


--
-- Name: h_variation; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.h_variation (
    variation_id integer DEFAULT 0 NOT NULL,
    variation_name text DEFAULT ''::text NOT NULL,
    variation_value text DEFAULT ''::text NOT NULL,
    variation_category text DEFAULT ''::text NOT NULL,
    updated_at timestamp without time zone DEFAULT now() NOT NULL,
    schedule_id integer DEFAULT 0 NOT NULL,
    action_by integer DEFAULT 0 NOT NULL,
    action_at timestamp without time zone DEFAULT now() NOT NULL,
    action_flg integer DEFAULT 0 NOT NULL,
    original_by text DEFAULT ''::text NOT NULL
);


ALTER TABLE public.h_variation OWNER TO postgres;

--
-- Name: h_worked_wage; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.h_worked_wage (
    worked_wage_id integer NOT NULL,
    extra_ratio real DEFAULT '0'::real NOT NULL,
    overtime_wage real DEFAULT '0'::real NOT NULL,
    basic real DEFAULT '0'::real NOT NULL,
    total_overtime real DEFAULT '0'::real NOT NULL,
    total real DEFAULT '0'::real NOT NULL,
    approved_id integer DEFAULT 0 NOT NULL,
    action_at timestamp without time zone DEFAULT now() NOT NULL,
    action_by integer DEFAULT 0 NOT NULL,
    extra_id integer DEFAULT 0 NOT NULL,
    overtime_title text DEFAULT ''::text NOT NULL,
    overtime text DEFAULT ''::text NOT NULL
);


ALTER TABLE public.h_worked_wage OWNER TO postgres;

--
-- Name: h_worked_wage_worked_wage_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.h_worked_wage_worked_wage_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.h_worked_wage_worked_wage_id_seq OWNER TO postgres;

--
-- Name: h_worked_wage_worked_wage_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.h_worked_wage_worked_wage_id_seq OWNED BY public.h_worked_wage.worked_wage_id;


--
-- Name: m_group; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.m_group (
    group_id integer NOT NULL,
    group_name text DEFAULT ''::text NOT NULL,
    updated_at timestamp without time zone DEFAULT now() NOT NULL,
    password text DEFAULT ''::text NOT NULL,
    open_time time without time zone DEFAULT '00:00:00'::time without time zone NOT NULL,
    close_time time without time zone DEFAULT '00:00:00'::time without time zone NOT NULL,
    area_id json DEFAULT '[]'::json NOT NULL,
    token text DEFAULT ''::text NOT NULL
);


ALTER TABLE public.m_group OWNER TO postgres;

--
-- Name: m_group_group_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.m_group_group_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.m_group_group_id_seq OWNER TO postgres;

--
-- Name: m_group_group_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.m_group_group_id_seq OWNED BY public.m_group.group_id;


--
-- Name: m_leave; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.m_leave (
    leave_id integer NOT NULL,
    leave_name text DEFAULT ''::text NOT NULL,
    paid_percent integer DEFAULT 100 NOT NULL,
    prove_flg integer DEFAULT 0 NOT NULL,
    group_id integer DEFAULT 0 NOT NULL,
    leave_amount_flg integer DEFAULT 0 NOT NULL,
    leave_days integer DEFAULT 1 NOT NULL,
    updated_at timestamp without time zone DEFAULT now() NOT NULL
);


ALTER TABLE public.m_leave OWNER TO postgres;

--
-- Name: m_leave_leave_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.m_leave_leave_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.m_leave_leave_id_seq OWNER TO postgres;

--
-- Name: m_leave_leave_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.m_leave_leave_id_seq OWNED BY public.m_leave.leave_id;


--
-- Name: m_menu; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.m_menu (
    menu_id integer NOT NULL,
    menu_name text DEFAULT ''::text NOT NULL,
    group_id integer DEFAULT 0 NOT NULL,
    updated_at timestamp without time zone DEFAULT now() NOT NULL
);


ALTER TABLE public.m_menu OWNER TO postgres;

--
-- Name: m_menu_menu_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.m_menu_menu_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.m_menu_menu_id_seq OWNER TO postgres;

--
-- Name: m_menu_menu_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.m_menu_menu_id_seq OWNED BY public.m_menu.menu_id;


--
-- Name: m_menu_necessary; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.m_menu_necessary (
    menu_necessary_id integer NOT NULL,
    menu_id integer DEFAULT 0 NOT NULL,
    service_id integer DEFAULT 0 NOT NULL,
    facility_id integer DEFAULT 0 NOT NULL,
    start_minute integer DEFAULT 0 NOT NULL,
    end_minute integer DEFAULT 0 NOT NULL,
    updated_at timestamp without time zone DEFAULT now() NOT NULL
);


ALTER TABLE public.m_menu_necessary OWNER TO postgres;

--
-- Name: m_menu_necessary_menu_necessary_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.m_menu_necessary_menu_necessary_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.m_menu_necessary_menu_necessary_id_seq OWNER TO postgres;

--
-- Name: m_menu_necessary_menu_necessary_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.m_menu_necessary_menu_necessary_id_seq OWNED BY public.m_menu_necessary.menu_necessary_id;


--
-- Name: m_service; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.m_service (
    service_id integer NOT NULL,
    service_name text DEFAULT ''::text NOT NULL,
    updated_at timestamp without time zone DEFAULT now() NOT NULL,
    area_id integer DEFAULT 0 NOT NULL
);


ALTER TABLE public.m_service OWNER TO postgres;

--
-- Name: m_service_id_service_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.m_service_id_service_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.m_service_id_service_id_seq OWNER TO postgres;

--
-- Name: m_service_id_service_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.m_service_id_service_id_seq OWNED BY public.m_service.service_id;


--
-- Name: r_ability; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.r_ability (
    usr_id integer DEFAULT 0 NOT NULL,
    service_id integer DEFAULT 0 NOT NULL,
    updated_at timestamp without time zone DEFAULT now() NOT NULL,
    priority integer DEFAULT 0 NOT NULL
);


ALTER TABLE public.r_ability OWNER TO postgres;

--
-- Name: r_extra; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.r_extra (
    extra_id integer NOT NULL,
    extra_start time without time zone,
    extra_end time without time zone,
    dayoff_flg integer DEFAULT 0 NOT NULL,
    usr_id integer DEFAULT 0 NOT NULL,
    group_id integer DEFAULT 0 NOT NULL,
    extra_ratio real DEFAULT '1'::real NOT NULL,
    over_flg integer DEFAULT 0 NOT NULL,
    hour_start integer DEFAULT 0 NOT NULL,
    updated_at timestamp without time zone DEFAULT now() NOT NULL
);


ALTER TABLE public.r_extra OWNER TO postgres;

--
-- Name: COLUMN r_extra.over_flg; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.r_extra.over_flg IS '1=month, 2=week, 3=day';


--
-- Name: COLUMN r_extra.hour_start; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.r_extra.hour_start IS 'with over_flg';


--
-- Name: r_extra_extra_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.r_extra_extra_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.r_extra_extra_id_seq OWNER TO postgres;

--
-- Name: r_extra_extra_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.r_extra_extra_id_seq OWNED BY public.r_extra.extra_id;


--
-- Name: r_group_relate; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.r_group_relate (
    group_relate_id integer NOT NULL,
    group_id integer DEFAULT 0 NOT NULL,
    usr_id integer DEFAULT 0 NOT NULL,
    priority integer DEFAULT 0 NOT NULL,
    updated_at timestamp without time zone DEFAULT now() NOT NULL,
    owner_flg integer DEFAULT 0 NOT NULL
);


ALTER TABLE public.r_group_relate OWNER TO postgres;

--
-- Name: TABLE r_group_relate; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON TABLE public.r_group_relate IS 'no usr_id for facility';


--
-- Name: r_group_relate_group_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.r_group_relate_group_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.r_group_relate_group_id_seq OWNER TO postgres;

--
-- Name: r_group_relate_group_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.r_group_relate_group_id_seq OWNED BY public.r_group_relate.group_relate_id;


--
-- Name: r_routine_routine_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.r_routine_routine_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.r_routine_routine_id_seq OWNER TO postgres;

--
-- Name: r_routine; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.r_routine (
    routine_id integer DEFAULT nextval('public.r_routine_routine_id_seq'::regclass) NOT NULL,
    usr_id integer DEFAULT 0 NOT NULL,
    start_0 time without time zone,
    end_0 time without time zone,
    start_1 time without time zone,
    end_1 time without time zone,
    start_2 time without time zone,
    end_2 time without time zone,
    start_3 time without time zone,
    end_3 time without time zone,
    start_4 time without time zone,
    end_4 time without time zone,
    start_5 time without time zone,
    end_5 time without time zone,
    start_6 time without time zone,
    end_6 time without time zone,
    group_id integer DEFAULT 0 NOT NULL,
    fix_flg smallint DEFAULT '0'::smallint NOT NULL,
    updated_at timestamp without time zone DEFAULT now() NOT NULL
);


ALTER TABLE public.r_routine OWNER TO postgres;

--
-- Name: COLUMN r_routine.fix_flg; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.r_routine.fix_flg IS '0=shift worker,1=permanent';


--
-- Name: r_rule; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.r_rule (
    usr_id integer DEFAULT 0 NOT NULL,
    group_id integer DEFAULT 0 NOT NULL,
    holiday_flg smallint DEFAULT '0'::smallint NOT NULL,
    approver1 integer DEFAULT 0 NOT NULL,
    approver2 integer DEFAULT 0 NOT NULL,
    compensatory_within smallint DEFAULT '0'::smallint NOT NULL,
    minimum_break smallint DEFAULT '0'::smallint NOT NULL,
    break_minute smallint DEFAULT '0'::smallint NOT NULL,
    wage real DEFAULT '0'::real NOT NULL,
    currency smallint DEFAULT '1'::smallint NOT NULL,
    updated_at timestamp without time zone DEFAULT now() NOT NULL,
    compensatory_flg smallint DEFAULT '0'::smallint NOT NULL
);


ALTER TABLE public.r_rule OWNER TO postgres;

--
-- Name: COLUMN r_rule.compensatory_within; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.r_rule.compensatory_within IS 'day unit';


--
-- Name: COLUMN r_rule.minimum_break; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.r_rule.minimum_break IS 'you must take break if you work more than this hour';


--
-- Name: COLUMN r_rule.break_minute; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.r_rule.break_minute IS 'minimum_break take this minutes';


--
-- Name: COLUMN r_rule.currency; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.r_rule.currency IS '1=yen,2=usd,3=eur';


--
-- Name: COLUMN r_rule.compensatory_flg; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.r_rule.compensatory_flg IS '1=get money after compensatory_end is over';


--
-- Name: t_compensatory; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.t_compensatory (
    compensatory_id integer NOT NULL,
    compensatory_start date DEFAULT now() NOT NULL,
    compensatory_end date DEFAULT now() NOT NULL,
    usr_id integer DEFAULT 0 NOT NULL,
    group_id integer DEFAULT 0 NOT NULL,
    schedule_id integer DEFAULT 0 NOT NULL,
    updated_at timestamp without time zone DEFAULT now() NOT NULL,
    compensatory_hours integer DEFAULT 0 NOT NULL,
    compensatory_days smallint DEFAULT '0'::smallint NOT NULL
);


ALTER TABLE public.t_compensatory OWNER TO postgres;

--
-- Name: COLUMN t_compensatory.compensatory_start; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.t_compensatory.compensatory_start IS 't_schedule.time_start';


--
-- Name: COLUMN t_compensatory.compensatory_end; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.t_compensatory.compensatory_end IS 't_schedule.time_start+r_rule.compensatory_within';


--
-- Name: t_compensatory_compensatory_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.t_compensatory_compensatory_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.t_compensatory_compensatory_id_seq OWNER TO postgres;

--
-- Name: t_compensatory_compensatory_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.t_compensatory_compensatory_id_seq OWNED BY public.t_compensatory.compensatory_id;


--
-- Name: t_usr; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.t_usr (
    usr_id integer NOT NULL,
    oauth_type integer DEFAULT 0 NOT NULL,
    oauth_id text DEFAULT ''::text NOT NULL,
    email text DEFAULT ''::text NOT NULL,
    usr_name text DEFAULT ''::text NOT NULL,
    created_at timestamp without time zone DEFAULT now() NOT NULL,
    updated_at timestamp without time zone DEFAULT now() NOT NULL,
    usr_name_mb text DEFAULT ''::text NOT NULL,
    password text DEFAULT ''::text NOT NULL,
    token text DEFAULT ''::text NOT NULL
);


ALTER TABLE public.t_usr OWNER TO postgres;

--
-- Name: COLUMN t_usr.oauth_type; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.t_usr.oauth_type IS '0 = none, 1 = gmail, 2 = facebook, 3 = email, 4 = custom';


--
-- Name: COLUMN t_usr.usr_name_mb; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.t_usr.usr_name_mb IS 'multi byte ja, ch, ko';


--
-- Name: t_usr_usr_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.t_usr_usr_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.t_usr_usr_id_seq OWNER TO postgres;

--
-- Name: t_usr_usr_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.t_usr_usr_id_seq OWNED BY public.t_usr.usr_id;


--
-- Name: t_facility; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.t_facility (
    facility_id integer DEFAULT nextval('public.t_usr_usr_id_seq'::regclass) NOT NULL,
    facility_name text DEFAULT ''::text NOT NULL,
    updated_at timestamp without time zone DEFAULT now() NOT NULL,
    amount integer DEFAULT 0 NOT NULL,
    group_id integer DEFAULT 0 NOT NULL
);


ALTER TABLE public.t_facility OWNER TO postgres;

--
-- Name: COLUMN t_facility.facility_id; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.t_facility.facility_id IS 'usr_id';


--
-- Name: t_facility_facility_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.t_facility_facility_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.t_facility_facility_id_seq OWNER TO postgres;

--
-- Name: t_leave_amount; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.t_leave_amount (
    usr_id integer DEFAULT 0 NOT NULL,
    enable_start date DEFAULT now() NOT NULL,
    enable_end date DEFAULT now() NOT NULL,
    grant_days integer DEFAULT 0 NOT NULL,
    used_days integer DEFAULT 0 NOT NULL,
    note text DEFAULT ''::text NOT NULL,
    updated_at timestamp without time zone DEFAULT now() NOT NULL,
    leave_id integer DEFAULT 0 NOT NULL
);


ALTER TABLE public.t_leave_amount OWNER TO postgres;

--
-- Name: t_schedule; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.t_schedule (
    schedule_id integer NOT NULL,
    time_start timestamp without time zone DEFAULT now() NOT NULL,
    time_end timestamp without time zone DEFAULT now() NOT NULL,
    title character varying(20) DEFAULT ''::character varying NOT NULL,
    usr_id integer DEFAULT 0 NOT NULL,
    tag integer DEFAULT 0 NOT NULL,
    group_id integer DEFAULT 0 NOT NULL,
    public_title character varying(20) DEFAULT ''::character varying NOT NULL,
    updated_at timestamp without time zone DEFAULT now() NOT NULL,
    access_right integer DEFAULT 666 NOT NULL,
    book_id integer DEFAULT 0 NOT NULL
);


ALTER TABLE public.t_schedule OWNER TO postgres;

--
-- Name: COLUMN t_schedule.usr_id; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.t_schedule.usr_id IS 'facility_id too';


--
-- Name: COLUMN t_schedule.tag; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.t_schedule.tag IS '1=meeting, 2=off, 3=out, 4=task, 5=shift, 6=service, 7=facility';


--
-- Name: COLUMN t_schedule.group_id; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.t_schedule.group_id IS '0 = todo only, 1 = all, 2 ~ group_type_id';


--
-- Name: t_schedule_schedule_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.t_schedule_schedule_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.t_schedule_schedule_id_seq OWNER TO postgres;

--
-- Name: t_schedule_schedule_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.t_schedule_schedule_id_seq OWNED BY public.t_schedule.schedule_id;


--
-- Name: t_sync; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.t_sync (
    sync_id integer NOT NULL,
    usr_id integer DEFAULT 0 NOT NULL,
    sync_token text DEFAULT ''::text NOT NULL,
    updated_at timestamp without time zone DEFAULT now() NOT NULL
);


ALTER TABLE public.t_sync OWNER TO postgres;

--
-- Name: t_sync_sync_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.t_sync_sync_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.t_sync_sync_id_seq OWNER TO postgres;

--
-- Name: t_sync_sync_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.t_sync_sync_id_seq OWNED BY public.t_sync.sync_id;


--
-- Name: t_timestamp; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.t_timestamp (
    timestamp_id integer NOT NULL,
    time_in timestamp without time zone DEFAULT now() NOT NULL,
    time_out timestamp without time zone,
    usr_id integer DEFAULT 0 NOT NULL,
    longitude double precision DEFAULT 0 NOT NULL,
    latitude double precision DEFAULT 0 NOT NULL,
    group_id integer DEFAULT 0 NOT NULL,
    manual_flg smallint DEFAULT '0'::smallint NOT NULL,
    approved_id integer DEFAULT 0 NOT NULL,
    private_ip inet DEFAULT '0.0.0.0'::inet NOT NULL,
    public_ip inet DEFAULT '0.0.0.0'::inet NOT NULL,
    break_at timestamp without time zone DEFAULT now() NOT NULL,
    break_amount integer DEFAULT 0 NOT NULL,
    updated_at timestamp without time zone DEFAULT now() NOT NULL,
    longitude2 double precision DEFAULT '0'::double precision NOT NULL,
    latitude2 double precision DEFAULT '0'::double precision NOT NULL,
    public_ip2 inet DEFAULT '0.0.0.0'::inet NOT NULL
);


ALTER TABLE public.t_timestamp OWNER TO postgres;

--
-- Name: t_timestamp_timestamp_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.t_timestamp_timestamp_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.t_timestamp_timestamp_id_seq OWNER TO postgres;

--
-- Name: t_timestamp_timestamp_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.t_timestamp_timestamp_id_seq OWNED BY public.t_timestamp.timestamp_id;


--
-- Name: t_todo; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.t_todo (
    todo_id integer NOT NULL,
    todo text DEFAULT ''::text NOT NULL,
    file_paths json,
    updated_at timestamp without time zone DEFAULT now() NOT NULL,
    schedule_id integer DEFAULT 0 NOT NULL
);


ALTER TABLE public.t_todo OWNER TO postgres;

--
-- Name: COLUMN t_todo.file_paths; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.t_todo.file_paths IS '[path,name]';


--
-- Name: t_todo_todo_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.t_todo_todo_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.t_todo_todo_id_seq OWNER TO postgres;

--
-- Name: t_todo_todo_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.t_todo_todo_id_seq OWNED BY public.t_todo.todo_id;


--
-- Name: t_variation; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.t_variation (
    variation_id integer NOT NULL,
    variation_name text DEFAULT ''::text NOT NULL,
    variation_value text DEFAULT ''::text NOT NULL,
    variation_category text DEFAULT ''::text NOT NULL,
    updated_at timestamp without time zone DEFAULT now() NOT NULL,
    schedule_id smallint DEFAULT '0'::smallint NOT NULL
);


ALTER TABLE public.t_variation OWNER TO postgres;

--
-- Name: t_variation_variation_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.t_variation_variation_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.t_variation_variation_id_seq OWNER TO postgres;

--
-- Name: t_variation_variation_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.t_variation_variation_id_seq OWNED BY public.t_variation.variation_id;


--
-- Name: c_area area_id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.c_area ALTER COLUMN area_id SET DEFAULT nextval('public.c_area_area_id_seq'::regclass);


--
-- Name: c_holiday holiday_id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.c_holiday ALTER COLUMN holiday_id SET DEFAULT nextval('public.c_holiday_holiday_id_seq'::regclass);


--
-- Name: c_link link_id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.c_link ALTER COLUMN link_id SET DEFAULT nextval('public.c_link_link_id_seq'::regclass);


--
-- Name: h_worked_wage worked_wage_id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.h_worked_wage ALTER COLUMN worked_wage_id SET DEFAULT nextval('public.h_worked_wage_worked_wage_id_seq'::regclass);


--
-- Name: m_group group_id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.m_group ALTER COLUMN group_id SET DEFAULT nextval('public.m_group_group_id_seq'::regclass);


--
-- Name: m_leave leave_id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.m_leave ALTER COLUMN leave_id SET DEFAULT nextval('public.m_leave_leave_id_seq'::regclass);


--
-- Name: m_menu menu_id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.m_menu ALTER COLUMN menu_id SET DEFAULT nextval('public.m_menu_menu_id_seq'::regclass);


--
-- Name: m_menu_necessary menu_necessary_id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.m_menu_necessary ALTER COLUMN menu_necessary_id SET DEFAULT nextval('public.m_menu_necessary_menu_necessary_id_seq'::regclass);


--
-- Name: m_service service_id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.m_service ALTER COLUMN service_id SET DEFAULT nextval('public.m_service_id_service_id_seq'::regclass);


--
-- Name: r_extra extra_id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.r_extra ALTER COLUMN extra_id SET DEFAULT nextval('public.r_extra_extra_id_seq'::regclass);


--
-- Name: r_group_relate group_relate_id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.r_group_relate ALTER COLUMN group_relate_id SET DEFAULT nextval('public.r_group_relate_group_id_seq'::regclass);


--
-- Name: t_compensatory compensatory_id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.t_compensatory ALTER COLUMN compensatory_id SET DEFAULT nextval('public.t_compensatory_compensatory_id_seq'::regclass);


--
-- Name: t_schedule schedule_id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.t_schedule ALTER COLUMN schedule_id SET DEFAULT nextval('public.t_schedule_schedule_id_seq'::regclass);


--
-- Name: t_sync sync_id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.t_sync ALTER COLUMN sync_id SET DEFAULT nextval('public.t_sync_sync_id_seq'::regclass);


--
-- Name: t_timestamp timestamp_id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.t_timestamp ALTER COLUMN timestamp_id SET DEFAULT nextval('public.t_timestamp_timestamp_id_seq'::regclass);


--
-- Name: t_todo todo_id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.t_todo ALTER COLUMN todo_id SET DEFAULT nextval('public.t_todo_todo_id_seq'::regclass);


--
-- Name: t_usr usr_id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.t_usr ALTER COLUMN usr_id SET DEFAULT nextval('public.t_usr_usr_id_seq'::regclass);


--
-- Name: t_variation variation_id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.t_variation ALTER COLUMN variation_id SET DEFAULT nextval('public.t_variation_variation_id_seq'::regclass);


--
-- Name: m_group m_group_type_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.m_group
    ADD CONSTRAINT m_group_type_pkey PRIMARY KEY (group_id);


--
-- Name: m_leave m_leave_leave_id; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.m_leave
    ADD CONSTRAINT m_leave_leave_id PRIMARY KEY (leave_id);


--
-- Name: m_service m_service_service_id; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.m_service
    ADD CONSTRAINT m_service_service_id PRIMARY KEY (service_id);


--
-- Name: r_ability r_ability_usr_id_service_id; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.r_ability
    ADD CONSTRAINT r_ability_usr_id_service_id PRIMARY KEY (usr_id, service_id);


--
-- Name: r_group_relate r_group_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.r_group_relate
    ADD CONSTRAINT r_group_pkey PRIMARY KEY (group_relate_id);


--
-- Name: r_routine r_routine_routine_id; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.r_routine
    ADD CONSTRAINT r_routine_routine_id PRIMARY KEY (routine_id);


--
-- Name: r_rule r_rule_usr_id_group_id; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.r_rule
    ADD CONSTRAINT r_rule_usr_id_group_id PRIMARY KEY (usr_id, group_id);


--
-- Name: t_compensatory t_compensatory_compensatory_id; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.t_compensatory
    ADD CONSTRAINT t_compensatory_compensatory_id PRIMARY KEY (compensatory_id);


--
-- Name: t_leave_amount t_leave_amount_usr_id_leave_id; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.t_leave_amount
    ADD CONSTRAINT t_leave_amount_usr_id_leave_id PRIMARY KEY (usr_id, leave_id);


--
-- Name: m_menu t_menu_menu_id; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.m_menu
    ADD CONSTRAINT t_menu_menu_id PRIMARY KEY (menu_id);


--
-- Name: m_menu_necessary t_menu_necessary_menu_necessary_id; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.m_menu_necessary
    ADD CONSTRAINT t_menu_necessary_menu_necessary_id PRIMARY KEY (menu_necessary_id);


--
-- Name: t_schedule t_schedule_schedule_id_usr_id; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.t_schedule
    ADD CONSTRAINT t_schedule_schedule_id_usr_id PRIMARY KEY (schedule_id, usr_id);


--
-- Name: t_timestamp t_timestamp_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.t_timestamp
    ADD CONSTRAINT t_timestamp_pkey PRIMARY KEY (timestamp_id);


--
-- Name: t_todo t_todo_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.t_todo
    ADD CONSTRAINT t_todo_pkey PRIMARY KEY (todo_id);


--
-- Name: t_usr t_usr_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.t_usr
    ADD CONSTRAINT t_usr_pkey PRIMARY KEY (usr_id);


--
-- PostgreSQL database dump complete
--

