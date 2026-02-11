--
-- PostgreSQL database dump
--

\restrict wDHDvok5sC8tmpyKpnbu6W9HyEfDdQKlq0ojwS0GphuHkFAboXJmoOGL4c8COJ1

-- Dumped from database version 18.1
-- Dumped by pg_dump version 18.1

-- Started on 2026-02-11 13:35:08

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
-- TOC entry 5041 (class 0 OID 0)
-- Dependencies: 4
-- Name: SCHEMA public; Type: COMMENT; Schema: -; Owner: pg_database_owner
--

COMMENT ON SCHEMA public IS 'standard public schema';


SET default_tablespace = '';

SET default_table_access_method = heap;

--
-- TOC entry 223 (class 1259 OID 16428)
-- Name: faq; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.faq (
    username character varying(30) NOT NULL,
    recensioni character varying(256) NOT NULL
);


ALTER TABLE public.faq OWNER TO postgres;

--
-- TOC entry 219 (class 1259 OID 16390)
-- Name: giochi; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.giochi (
    nome_gioco character varying(50) NOT NULL,
    immagine character varying(255)
);


ALTER TABLE public.giochi OWNER TO postgres;

--
-- TOC entry 220 (class 1259 OID 16394)
-- Name: prenotazioni; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.prenotazioni (
    id_prenotazione integer NOT NULL,
    username_utente character varying(30),
    nome_gioco character varying(50),
    data_ora timestamp without time zone NOT NULL,
    numero_pista integer,
    numero_tavolo integer,
    numero_persone integer,
    partecipazione_torneo boolean DEFAULT false
);


ALTER TABLE public.prenotazioni OWNER TO postgres;

--
-- TOC entry 221 (class 1259 OID 16400)
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
-- TOC entry 5045 (class 0 OID 0)
-- Dependencies: 221
-- Name: prenotazioni_id_prenotazione_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.prenotazioni_id_prenotazione_seq OWNED BY public.prenotazioni.id_prenotazione;


--
-- TOC entry 222 (class 1259 OID 16401)
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
-- TOC entry 4868 (class 2604 OID 16433)
-- Name: prenotazioni id_prenotazione; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.prenotazioni ALTER COLUMN id_prenotazione SET DEFAULT nextval('public.prenotazioni_id_prenotazione_seq'::regclass);


--
-- TOC entry 5035 (class 0 OID 16428)
-- Dependencies: 223
-- Data for Name: faq; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.faq (username, recensioni) FROM stdin;
\.


--
-- TOC entry 5031 (class 0 OID 16390)
-- Dependencies: 219
-- Data for Name: giochi; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.giochi (nome_gioco, immagine) FROM stdin;
Bowling	img/bowling.jpg
Biliardo	img/biliardo.jpg
Laser Game	img/laser-game.jpg
Carte	img/carte.jpg
\.


--
-- TOC entry 5032 (class 0 OID 16394)
-- Dependencies: 220
-- Data for Name: prenotazioni; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.prenotazioni (id_prenotazione, username_utente, nome_gioco, data_ora, numero_pista, numero_tavolo, numero_persone, partecipazione_torneo) FROM stdin;
23	pipa	Biliardo	2026-02-11 17:00:00	\N	1	\N	\N
25	pipa	Biliardo	2026-02-11 17:00:00	\N	2	\N	\N
26	pipa	Bowling	2026-02-11 17:00:00	1	\N	\N	\N
28	pipa	Bowling	2026-02-11 17:00:00	6	\N	\N	\N
\.


--
-- TOC entry 5034 (class 0 OID 16401)
-- Dependencies: 222
-- Data for Name: utenti; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.utenti (username, nome_completo, email, pass) FROM stdin;
pipa	MARTINA	martina0turi@gmail.com	$2y$10$iFcxUB2Ct8/1itk4C8TsruPeaOggjrZ6oMMhquneZqxRdcHiSnnse
\.


--
-- TOC entry 5048 (class 0 OID 0)
-- Dependencies: 221
-- Name: prenotazioni_id_prenotazione_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.prenotazioni_id_prenotazione_seq', 28, true);


--
-- TOC entry 4881 (class 2606 OID 16435)
-- Name: faq faq_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.faq
    ADD CONSTRAINT faq_pkey PRIMARY KEY (username);


--
-- TOC entry 4871 (class 2606 OID 16411)
-- Name: giochi giochi_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.giochi
    ADD CONSTRAINT giochi_pkey PRIMARY KEY (nome_gioco);


--
-- TOC entry 4873 (class 2606 OID 16413)
-- Name: prenotazioni prenotazioni_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.prenotazioni
    ADD CONSTRAINT prenotazioni_pkey PRIMARY KEY (id_prenotazione);


--
-- TOC entry 4877 (class 2606 OID 16415)
-- Name: utenti utenti_email_key; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.utenti
    ADD CONSTRAINT utenti_email_key UNIQUE (email);


--
-- TOC entry 4879 (class 2606 OID 16417)
-- Name: utenti utenti_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.utenti
    ADD CONSTRAINT utenti_pkey PRIMARY KEY (username);


--
-- TOC entry 4874 (class 1259 OID 16440)
-- Name: unica_prenotazione_pista; Type: INDEX; Schema: public; Owner: postgres
--

CREATE UNIQUE INDEX unica_prenotazione_pista ON public.prenotazioni USING btree (nome_gioco, data_ora, numero_pista) WHERE (numero_pista IS NOT NULL);


--
-- TOC entry 4875 (class 1259 OID 16439)
-- Name: unica_prenotazione_tavolo; Type: INDEX; Schema: public; Owner: postgres
--

CREATE UNIQUE INDEX unica_prenotazione_tavolo ON public.prenotazioni USING btree (nome_gioco, data_ora, numero_tavolo) WHERE (numero_tavolo IS NOT NULL);


--
-- TOC entry 4882 (class 2606 OID 16418)
-- Name: prenotazioni prenotazioni_nome_gioco_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.prenotazioni
    ADD CONSTRAINT prenotazioni_nome_gioco_fkey FOREIGN KEY (nome_gioco) REFERENCES public.giochi(nome_gioco) ON UPDATE CASCADE;


--
-- TOC entry 4883 (class 2606 OID 16423)
-- Name: prenotazioni prenotazioni_username_utente_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.prenotazioni
    ADD CONSTRAINT prenotazioni_username_utente_fkey FOREIGN KEY (username_utente) REFERENCES public.utenti(username) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 5042 (class 0 OID 0)
-- Dependencies: 223
-- Name: TABLE faq; Type: ACL; Schema: public; Owner: postgres
--

GRANT ALL ON TABLE public.faq TO www;


--
-- TOC entry 5043 (class 0 OID 0)
-- Dependencies: 219
-- Name: TABLE giochi; Type: ACL; Schema: public; Owner: postgres
--

GRANT ALL ON TABLE public.giochi TO www;


--
-- TOC entry 5044 (class 0 OID 0)
-- Dependencies: 220
-- Name: TABLE prenotazioni; Type: ACL; Schema: public; Owner: postgres
--

GRANT ALL ON TABLE public.prenotazioni TO www;


--
-- TOC entry 5046 (class 0 OID 0)
-- Dependencies: 221
-- Name: SEQUENCE prenotazioni_id_prenotazione_seq; Type: ACL; Schema: public; Owner: postgres
--

GRANT SELECT,USAGE ON SEQUENCE public.prenotazioni_id_prenotazione_seq TO www;


--
-- TOC entry 5047 (class 0 OID 0)
-- Dependencies: 222
-- Name: TABLE utenti; Type: ACL; Schema: public; Owner: postgres
--

GRANT ALL ON TABLE public.utenti TO www;


-- Completed on 2026-02-11 13:35:09

--
-- PostgreSQL database dump complete
--

\unrestrict wDHDvok5sC8tmpyKpnbu6W9HyEfDdQKlq0ojwS0GphuHkFAboXJmoOGL4c8COJ1

