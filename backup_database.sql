--
-- PostgreSQL database dump
--

\restrict UcjgoddS76uOAAhOXufFOl4ziLFKT22pEbat2EmciwpKOUk1DTfiORWTinsDBSl

-- Dumped from database version 18.1
-- Dumped by pg_dump version 18.1

-- Started on 2026-02-21 12:11:04

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
-- TOC entry 5046 (class 0 OID 0)
-- Dependencies: 4
-- Name: SCHEMA public; Type: COMMENT; Schema: -; Owner: pg_database_owner
--

COMMENT ON SCHEMA public IS 'standard public schema';


SET default_tablespace = '';

SET default_table_access_method = heap;

--
-- TOC entry 224 (class 1259 OID 16677)
-- Name: faq; Type: TABLE; Schema: public; Owner: www
--

CREATE TABLE public.faq (
    id_recensione integer NOT NULL,
    username character varying(30) NOT NULL,
    recensione character varying(300) NOT NULL,
    data_recensione timestamp without time zone DEFAULT CURRENT_TIMESTAMP,
    stelle integer,
    CONSTRAINT faq_stelle_check CHECK (((stelle >= 1) AND (stelle <= 5)))
);


ALTER TABLE public.faq OWNER TO www;

--
-- TOC entry 223 (class 1259 OID 16676)
-- Name: faq_id_recensione_seq; Type: SEQUENCE; Schema: public; Owner: www
--

CREATE SEQUENCE public.faq_id_recensione_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.faq_id_recensione_seq OWNER TO www;

--
-- TOC entry 5047 (class 0 OID 0)
-- Dependencies: 223
-- Name: faq_id_recensione_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: www
--

ALTER SEQUENCE public.faq_id_recensione_seq OWNED BY public.faq.id_recensione;


--
-- TOC entry 219 (class 1259 OID 16637)
-- Name: giochi; Type: TABLE; Schema: public; Owner: www
--

CREATE TABLE public.giochi (
    nome_gioco character varying(50) NOT NULL,
    immagine character varying(255)
);


ALTER TABLE public.giochi OWNER TO www;

--
-- TOC entry 222 (class 1259 OID 16656)
-- Name: prenotazioni; Type: TABLE; Schema: public; Owner: www
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


ALTER TABLE public.prenotazioni OWNER TO www;

--
-- TOC entry 221 (class 1259 OID 16655)
-- Name: prenotazioni_id_prenotazione_seq; Type: SEQUENCE; Schema: public; Owner: www
--

CREATE SEQUENCE public.prenotazioni_id_prenotazione_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.prenotazioni_id_prenotazione_seq OWNER TO www;

--
-- TOC entry 5048 (class 0 OID 0)
-- Dependencies: 221
-- Name: prenotazioni_id_prenotazione_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: www
--

ALTER SEQUENCE public.prenotazioni_id_prenotazione_seq OWNED BY public.prenotazioni.id_prenotazione;


--
-- TOC entry 220 (class 1259 OID 16643)
-- Name: utenti; Type: TABLE; Schema: public; Owner: www
--

CREATE TABLE public.utenti (
    username character varying(30) NOT NULL,
    nome_completo character varying(50) NOT NULL,
    email character varying(100) NOT NULL,
    pass text
);


ALTER TABLE public.utenti OWNER TO www;

--
-- TOC entry 4870 (class 2604 OID 16680)
-- Name: faq id_recensione; Type: DEFAULT; Schema: public; Owner: www
--

ALTER TABLE ONLY public.faq ALTER COLUMN id_recensione SET DEFAULT nextval('public.faq_id_recensione_seq'::regclass);


--
-- TOC entry 4869 (class 2604 OID 16659)
-- Name: prenotazioni id_prenotazione; Type: DEFAULT; Schema: public; Owner: www
--

ALTER TABLE ONLY public.prenotazioni ALTER COLUMN id_prenotazione SET DEFAULT nextval('public.prenotazioni_id_prenotazione_seq'::regclass);


--
-- TOC entry 5040 (class 0 OID 16677)
-- Dependencies: 224
-- Data for Name: faq; Type: TABLE DATA; Schema: public; Owner: www
--

COPY public.faq (id_recensione, username, recensione, data_recensione, stelle) FROM stdin;
1	mario_rossi	Le piste da bowling sono fantastiche!	2026-02-21 12:06:01.696391	5
2	giulia_b	Bella serata, ma i tavoli da biliardo erano quasi tutti occupati.	2026-02-21 12:06:01.696391	4
3	luca_v	Laser game adrenalinico, torneremo sicuramente!	2026-02-21 12:06:01.696391	5
4	admin_user	Sistema di prenotazione molto semplice da usare.	2026-02-21 12:06:01.696391	4
\.


--
-- TOC entry 5035 (class 0 OID 16637)
-- Dependencies: 219
-- Data for Name: giochi; Type: TABLE DATA; Schema: public; Owner: www
--

COPY public.giochi (nome_gioco, immagine) FROM stdin;
Bowling	resources/img_giochi/bowling.jpg
Biliardo	resources/img_giochi/biliardo.jpg
Torneo di Carte	resources/img_giochi/torneo_di_carte.jpg
Laser Game	resources/img_giochi/laser_game.jpg
\.


--
-- TOC entry 5038 (class 0 OID 16656)
-- Dependencies: 222
-- Data for Name: prenotazioni; Type: TABLE DATA; Schema: public; Owner: www
--

COPY public.prenotazioni (id_prenotazione, username_utente, nome_gioco, data_ora, numero_pista, numero_tavolo, numero_persone) FROM stdin;
1	mario_rossi	Bowling	2026-03-01 18:00:00	5	\N	4
2	giulia_b	Biliardo	2026-03-01 21:30:00	\N	3	2
3	luca_v	Laser Game	2026-03-02 15:00:00	\N	\N	10
4	admin_user	Torneo di Carte	2026-03-05 20:00:00	\N	\N	1
5	mario_rossi	Bowling	2026-03-10 19:00:00	12	\N	5
\.


--
-- TOC entry 5036 (class 0 OID 16643)
-- Dependencies: 220
-- Data for Name: utenti; Type: TABLE DATA; Schema: public; Owner: www
--

COPY public.utenti (username, nome_completo, email, pass) FROM stdin;
admin_user	Admin Test	admin@example.com	$2y$10$iFcxUB2Ct8/1itk4C8TsruPeaOggjrZ6oMMhquneZqxRdcHiSnnse
mario_rossi	Mario Rossi	mario@rossi.it	$2y$10$d5vSk7NAKGbqpWS7NBs8GOflEDfN0w4Nk1ynYhgRBCK0WbYJsn/d6
giulia_b	Giulia Bianchi	giulia@provider.it	$2y$10$9ST7O42ymNVKIX6H.QoabuDCwlWJe2m1ZaNwU21D/wWMfG5Mntt1m
luca_v	Luca Verdi	luca@mail.com	$2y$10$nWjb8.oUNeSuQbs.NyckheoWcINegTdjhEruLLIzMrSj2NuOhGZqy
pipa	PincoPallino	pincopallino@gioco.it	555
\.


--
-- TOC entry 5049 (class 0 OID 0)
-- Dependencies: 223
-- Name: faq_id_recensione_seq; Type: SEQUENCE SET; Schema: public; Owner: www
--

SELECT pg_catalog.setval('public.faq_id_recensione_seq', 4, true);


--
-- TOC entry 5050 (class 0 OID 0)
-- Dependencies: 221
-- Name: prenotazioni_id_prenotazione_seq; Type: SEQUENCE SET; Schema: public; Owner: www
--

SELECT pg_catalog.setval('public.prenotazioni_id_prenotazione_seq', 5, true);


--
-- TOC entry 4884 (class 2606 OID 16687)
-- Name: faq faq_pkey; Type: CONSTRAINT; Schema: public; Owner: www
--

ALTER TABLE ONLY public.faq
    ADD CONSTRAINT faq_pkey PRIMARY KEY (id_recensione);


--
-- TOC entry 4874 (class 2606 OID 16642)
-- Name: giochi giochi_pkey; Type: CONSTRAINT; Schema: public; Owner: www
--

ALTER TABLE ONLY public.giochi
    ADD CONSTRAINT giochi_pkey PRIMARY KEY (nome_gioco);


--
-- TOC entry 4880 (class 2606 OID 16663)
-- Name: prenotazioni prenotazioni_pkey; Type: CONSTRAINT; Schema: public; Owner: www
--

ALTER TABLE ONLY public.prenotazioni
    ADD CONSTRAINT prenotazioni_pkey PRIMARY KEY (id_prenotazione);


--
-- TOC entry 4876 (class 2606 OID 16654)
-- Name: utenti utenti_email_key; Type: CONSTRAINT; Schema: public; Owner: www
--

ALTER TABLE ONLY public.utenti
    ADD CONSTRAINT utenti_email_key UNIQUE (email);


--
-- TOC entry 4878 (class 2606 OID 16652)
-- Name: utenti utenti_pkey; Type: CONSTRAINT; Schema: public; Owner: www
--

ALTER TABLE ONLY public.utenti
    ADD CONSTRAINT utenti_pkey PRIMARY KEY (username);


--
-- TOC entry 4881 (class 1259 OID 16674)
-- Name: unica_prenotazione_pista; Type: INDEX; Schema: public; Owner: www
--

CREATE UNIQUE INDEX unica_prenotazione_pista ON public.prenotazioni USING btree (nome_gioco, data_ora, numero_pista) WHERE (numero_pista IS NOT NULL);


--
-- TOC entry 4882 (class 1259 OID 16675)
-- Name: unica_prenotazione_tavolo; Type: INDEX; Schema: public; Owner: www
--

CREATE UNIQUE INDEX unica_prenotazione_tavolo ON public.prenotazioni USING btree (nome_gioco, data_ora, numero_tavolo) WHERE (numero_tavolo IS NOT NULL);


--
-- TOC entry 4887 (class 2606 OID 16688)
-- Name: faq faq_username_fkey; Type: FK CONSTRAINT; Schema: public; Owner: www
--

ALTER TABLE ONLY public.faq
    ADD CONSTRAINT faq_username_fkey FOREIGN KEY (username) REFERENCES public.utenti(username) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 4885 (class 2606 OID 16669)
-- Name: prenotazioni prenotazioni_nome_gioco_fkey; Type: FK CONSTRAINT; Schema: public; Owner: www
--

ALTER TABLE ONLY public.prenotazioni
    ADD CONSTRAINT prenotazioni_nome_gioco_fkey FOREIGN KEY (nome_gioco) REFERENCES public.giochi(nome_gioco) ON UPDATE CASCADE;


--
-- TOC entry 4886 (class 2606 OID 16664)
-- Name: prenotazioni prenotazioni_username_utente_fkey; Type: FK CONSTRAINT; Schema: public; Owner: www
--

ALTER TABLE ONLY public.prenotazioni
    ADD CONSTRAINT prenotazioni_username_utente_fkey FOREIGN KEY (username_utente) REFERENCES public.utenti(username) ON UPDATE CASCADE ON DELETE CASCADE;


-- Completed on 2026-02-21 12:11:04

--
-- PostgreSQL database dump complete
--

\unrestrict UcjgoddS76uOAAhOXufFOl4ziLFKT22pEbat2EmciwpKOUk1DTfiORWTinsDBSl

