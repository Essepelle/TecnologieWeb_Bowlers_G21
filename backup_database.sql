--
-- PostgreSQL database dump
--

\restrict brKAUVmSDATApzw34CABAtkGBW3nurEVbBH8vPxaOrL3YLRw2fgxgnq6CXnaRB7

-- Dumped from database version 18.1
-- Dumped by pg_dump version 18.1

-- Started on 2026-02-19 13:06:40

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

--
-- TOC entry 4 (class 2615 OID 2200)
-- Name: public; Type: SCHEMA; Schema: -; Owner: pg_database_owner
--

CREATE SCHEMA public;


ALTER SCHEMA public OWNER TO pg_database_owner;

--
-- TOC entry 5045 (class 0 OID 0)
-- Dependencies: 4
-- Name: SCHEMA public; Type: COMMENT; Schema: -; Owner: pg_database_owner
--

COMMENT ON SCHEMA public IS 'standard public schema';


SET default_tablespace = '';

SET default_table_access_method = heap;

--
-- TOC entry 223 (class 1259 OID 16612)
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
-- TOC entry 222 (class 1259 OID 16611)
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
-- TOC entry 5047 (class 0 OID 0)
-- Dependencies: 222
-- Name: faq_id_recensione_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.faq_id_recensione_seq OWNED BY public.faq.id_recensione;


--
-- TOC entry 224 (class 1259 OID 16631)
-- Name: giochi; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.giochi (
    nome_gioco character varying(50) NOT NULL,
    immagine character varying(255)
);


ALTER TABLE public.giochi OWNER TO postgres;

--
-- TOC entry 221 (class 1259 OID 16565)
-- Name: prenotazioni; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.prenotazioni (
    id_prenotazione integer NOT NULL,
    username_utente character varying(30),
    nome_gioco character varying(50),
    data_ora timestamp without time zone NOT NULL,
    numero_pista integer,
    numero_tavolo integer,
    numero_persone integer
);


ALTER TABLE public.prenotazioni OWNER TO postgres;

--
-- TOC entry 220 (class 1259 OID 16564)
-- Name: prenotazioni_id_prenotazione_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.prenotazioni_id_prenotazione_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.prenotazioni_id_prenotazione_seq OWNER TO postgres;

--
-- TOC entry 5051 (class 0 OID 0)
-- Dependencies: 220
-- Name: prenotazioni_id_prenotazione_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.prenotazioni_id_prenotazione_seq OWNED BY public.prenotazioni.id_prenotazione;


--
-- TOC entry 219 (class 1259 OID 16552)
-- Name: utenti; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.utenti (
    username character varying(30) NOT NULL,
    nome_completo character varying(50) NOT NULL,
    email character varying(100) NOT NULL,
    pass text
);


ALTER TABLE public.utenti OWNER TO postgres;

--
-- TOC entry 4870 (class 2604 OID 16615)
-- Name: faq id_recensione; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.faq ALTER COLUMN id_recensione SET DEFAULT nextval('public.faq_id_recensione_seq'::regclass);


--
-- TOC entry 4869 (class 2604 OID 16608)
-- Name: prenotazioni id_prenotazione; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.prenotazioni ALTER COLUMN id_prenotazione SET DEFAULT nextval('public.prenotazioni_id_prenotazione_seq'::regclass);


--
-- TOC entry 5038 (class 0 OID 16612)
-- Dependencies: 223
-- Data for Name: faq; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.faq (id_recensione, username, recensione, data_recensione, stelle) FROM stdin;
11	pipa	ciao vinni	2026-02-11 14:59:49.822448	2
12	pipa	ciao simone	2026-02-11 15:02:32.432438	3
13	pipa	ciao sabry	2026-02-11 15:04:54.697544	2
14	pipa	ciao marty	2026-02-11 15:06:08.282626	5
15	pipa	aa	2026-02-11 15:07:19.044131	3
16	pipa	aaa	2026-02-11 15:08:27.312135	1
17	sasasas	SERVIZI AL TOP, DIVERTIMENTO ASSICURATO	2026-02-12 21:42:09.927257	4
18	ginseng	Sica è stronzo	2026-02-13 09:25:01.051971	5
19	pipa	dd	2026-02-13 14:32:47.590402	3
20	pipa	mio	2026-02-15 01:18:48.706495	1
\.


--
-- TOC entry 5039 (class 0 OID 16631)
-- Dependencies: 224
-- Data for Name: giochi; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.giochi (nome_gioco, immagine) FROM stdin;
Bowling	resources/img_giochi/bowling.jpg
Biliardo	resources/img_giochi/biliardo.jpg
Torneo di Carte	resources/img_giochi/torneo_di_carte.jpg
Laser Game	resources/img_giochi/laser_game.jpg
\.


--
-- TOC entry 5036 (class 0 OID 16565)
-- Dependencies: 221
-- Data for Name: prenotazioni; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.prenotazioni (id_prenotazione, username_utente, nome_gioco, data_ora, numero_pista, numero_tavolo, numero_persone) FROM stdin;
68	pipa	Bowling	2026-02-20 18:30:00	12	\N	\N
71	pipa	Biliardo	2026-02-20 01:00:00	\N	4	\N
72	pipa	Biliardo	2026-02-20 17:30:00	\N	6	\N
74	pipa	Bowling	2026-02-27 18:30:00	14	\N	\N
75	pipa	Bowling	2026-02-20 17:00:00	11	\N	\N
77	pipa4	Bowling	2026-02-25 18:00:00	17	\N	\N
78	pipa	Bowling	2026-02-26 18:00:00	14	\N	\N
80	pipa	Torneo di Carte	2026-02-27 21:00:00	\N	\N	\N
81	pipa	Biliardo	2026-02-20 19:00:00	\N	4	\N
\.


--
-- TOC entry 5034 (class 0 OID 16552)
-- Dependencies: 219
-- Data for Name: utenti; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.utenti (username, nome_completo, email, pass) FROM stdin;
pipa	MARTINA	martina0turi@gmail.com	$2y$10$iFcxUB2Ct8/1itk4C8TsruPeaOggjrZ6oMMhquneZqxRdcHiSnnse
sasasas	antonio 	avalenza15@gmail.com	$2y$10$d5vSk7NAKGbqpWS7NBs8GOflEDfN0w4Nk1ynYhgRBCK0WbYJsn/d6
ginseng	Gabriele Imparato	mipiaceilcazzo@gmail.com	$2y$10$nWjb8.oUNeSuQbs.NyckheoWcINegTdjhEruLLIzMrSj2NuOhGZqy
antonio	PincoPallino	kkkkkk@a	$2y$10$9ST7O42ymNVKIX6H.QoabuDCwlWJe2m1ZaNwU21D/wWMfG5Mntt1m
admin_user	Admin Test	admin@example.com	55
pipa4	MARTINA4	martina04turi@gmail.com	$2y$10$PD/oek/Aw4zmPL96/jVMgufNXWuwgIttMNSpwrSayYpRosXE8TXTK
\.


--
-- TOC entry 5054 (class 0 OID 0)
-- Dependencies: 222
-- Name: faq_id_recensione_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.faq_id_recensione_seq', 20, true);


--
-- TOC entry 5055 (class 0 OID 0)
-- Dependencies: 220
-- Name: prenotazioni_id_prenotazione_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.prenotazioni_id_prenotazione_seq', 82, true);


--
-- TOC entry 4882 (class 2606 OID 16622)
-- Name: faq faq_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.faq
    ADD CONSTRAINT faq_pkey PRIMARY KEY (id_recensione);


--
-- TOC entry 4884 (class 2606 OID 16636)
-- Name: giochi giochi_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.giochi
    ADD CONSTRAINT giochi_pkey PRIMARY KEY (nome_gioco);


--
-- TOC entry 4878 (class 2606 OID 16573)
-- Name: prenotazioni prenotazioni_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.prenotazioni
    ADD CONSTRAINT prenotazioni_pkey PRIMARY KEY (id_prenotazione);


--
-- TOC entry 4874 (class 2606 OID 16563)
-- Name: utenti utenti_email_key; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.utenti
    ADD CONSTRAINT utenti_email_key UNIQUE (email);


--
-- TOC entry 4876 (class 2606 OID 16561)
-- Name: utenti utenti_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.utenti
    ADD CONSTRAINT utenti_pkey PRIMARY KEY (username);


--
-- TOC entry 4879 (class 1259 OID 16609)
-- Name: unica_prenotazione_pista; Type: INDEX; Schema: public; Owner: postgres
--

CREATE UNIQUE INDEX unica_prenotazione_pista ON public.prenotazioni USING btree (nome_gioco, data_ora, numero_pista) WHERE (numero_pista IS NOT NULL);


--
-- TOC entry 4880 (class 1259 OID 16610)
-- Name: unica_prenotazione_tavolo; Type: INDEX; Schema: public; Owner: postgres
--

CREATE UNIQUE INDEX unica_prenotazione_tavolo ON public.prenotazioni USING btree (nome_gioco, data_ora, numero_tavolo) WHERE (numero_tavolo IS NOT NULL);


--
-- TOC entry 4886 (class 2606 OID 16623)
-- Name: faq fk_utente; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.faq
    ADD CONSTRAINT fk_utente FOREIGN KEY (username) REFERENCES public.utenti(username) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 4885 (class 2606 OID 16574)
-- Name: prenotazioni prenotazioni_username_utente_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.prenotazioni
    ADD CONSTRAINT prenotazioni_username_utente_fkey FOREIGN KEY (username_utente) REFERENCES public.utenti(username) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 5046 (class 0 OID 0)
-- Dependencies: 223
-- Name: TABLE faq; Type: ACL; Schema: public; Owner: postgres
--

GRANT ALL ON TABLE public.faq TO www;


--
-- TOC entry 5048 (class 0 OID 0)
-- Dependencies: 222
-- Name: SEQUENCE faq_id_recensione_seq; Type: ACL; Schema: public; Owner: postgres
--

GRANT SELECT,USAGE ON SEQUENCE public.faq_id_recensione_seq TO www;


--
-- TOC entry 5049 (class 0 OID 0)
-- Dependencies: 224
-- Name: TABLE giochi; Type: ACL; Schema: public; Owner: postgres
--

GRANT ALL ON TABLE public.giochi TO www;


--
-- TOC entry 5050 (class 0 OID 0)
-- Dependencies: 221
-- Name: TABLE prenotazioni; Type: ACL; Schema: public; Owner: postgres
--

GRANT ALL ON TABLE public.prenotazioni TO www;


--
-- TOC entry 5052 (class 0 OID 0)
-- Dependencies: 220
-- Name: SEQUENCE prenotazioni_id_prenotazione_seq; Type: ACL; Schema: public; Owner: postgres
--

GRANT SELECT,USAGE ON SEQUENCE public.prenotazioni_id_prenotazione_seq TO www;


--
-- TOC entry 5053 (class 0 OID 0)
-- Dependencies: 219
-- Name: TABLE utenti; Type: ACL; Schema: public; Owner: postgres
--

GRANT ALL ON TABLE public.utenti TO www;


-- Completed on 2026-02-19 13:06:41

--
-- PostgreSQL database dump complete
--

\unrestrict brKAUVmSDATApzw34CABAtkGBW3nurEVbBH8vPxaOrL3YLRw2fgxgnq6CXnaRB7

