--
-- PostgreSQL database dump
--

\restrict xWpvIKhI0pjOJ5cJQwaRMo6CPEzNxg7hefCXjnccL7wC12OmiWPddFXcuNFcYUA

-- Dumped from database version 18.1
-- Dumped by pg_dump version 18.1

-- Started on 2026-02-11 17:26:21

SET statement_timeout = 0;
SET lock_timeout = 0;
SET idle_in_transaction_session_timeout = 0;
SET transaction_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SELECT pg_catalog.set_config('search_path', '', false);
SET check_function_bodies = false;
SET xmloption = content;
SET client_min_messages = warning;
SET row_security = off;

SET default_tablespace = '';

SET default_table_access_method = heap;

--
-- TOC entry 224 (class 1259 OID 16612)
-- Name: faq; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.faq (
    id_recensione integer NOT NULL,
    username character varying(30) NOT NULL,
    recensione character varying(300) NOT NULL,
    data_recensione timestamp without time zone DEFAULT CURRENT_TIMESTAMP,
    stelle integer,
    CONSTRAINT faq_stelle_check CHECK (((stelle >= 1) AND (stelle <= 5)))
);


ALTER TABLE public.faq OWNER TO postgres;

--
-- TOC entry 223 (class 1259 OID 16611)
-- Name: faq_id_recensione_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.faq_id_recensione_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.faq_id_recensione_seq OWNER TO postgres;

--
-- TOC entry 5026 (class 0 OID 0)
-- Dependencies: 223
-- Name: faq_id_recensione_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.faq_id_recensione_seq OWNED BY public.faq.id_recensione;


--
-- TOC entry 4865 (class 2604 OID 16615)
-- Name: faq id_recensione; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.faq ALTER COLUMN id_recensione SET DEFAULT nextval('public.faq_id_recensione_seq'::regclass);


--
-- TOC entry 5019 (class 0 OID 16612)
-- Dependencies: 224
-- Data for Name: faq; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.faq (id_recensione, username, recensione, data_recensione, stelle) FROM stdin;
11	pipa	ciao vinni	2026-02-11 14:59:49.822448	2
12	pipa	ciao simone	2026-02-11 15:02:32.432438	3
13	pipa	ciao sabry	2026-02-11 15:04:54.697544	2
14	pipa	ciao marty	2026-02-11 15:06:08.282626	5
15	pipa	aa	2026-02-11 15:07:19.044131	3
16	pipa	aaa	2026-02-11 15:08:27.312135	1
\.


--
-- TOC entry 5028 (class 0 OID 0)
-- Dependencies: 223
-- Name: faq_id_recensione_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.faq_id_recensione_seq', 16, true);


--
-- TOC entry 4869 (class 2606 OID 16622)
-- Name: faq faq_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.faq
    ADD CONSTRAINT faq_pkey PRIMARY KEY (id_recensione);


--
-- TOC entry 4870 (class 2606 OID 16623)
-- Name: faq fk_utente; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.faq
    ADD CONSTRAINT fk_utente FOREIGN KEY (username) REFERENCES public.utenti(username) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 5025 (class 0 OID 0)
-- Dependencies: 224
-- Name: TABLE faq; Type: ACL; Schema: public; Owner: postgres
--

GRANT ALL ON TABLE public.faq TO www;


--
-- TOC entry 5027 (class 0 OID 0)
-- Dependencies: 223
-- Name: SEQUENCE faq_id_recensione_seq; Type: ACL; Schema: public; Owner: postgres
--

GRANT SELECT,USAGE ON SEQUENCE public.faq_id_recensione_seq TO www;


-- Completed on 2026-02-11 17:26:21

--
-- PostgreSQL database dump complete
--

\unrestrict xWpvIKhI0pjOJ5cJQwaRMo6CPEzNxg7hefCXjnccL7wC12OmiWPddFXcuNFcYUA

