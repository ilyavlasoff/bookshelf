--
-- PostgreSQL database dump
--

-- Dumped from database version 12.3 (Debian 12.3-1.pgdg100+1)
-- Dumped by pg_dump version 12.3 (Ubuntu 12.3-1.pgdg18.04+1)

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

SET default_tablespace = '';

SET default_table_access_method = heap;

--
-- Name: book; Type: TABLE; Schema: public; Owner: bookshelf
--

CREATE TABLE public.book (
    id integer NOT NULL,
    owner integer,
    book_name character varying(255) NOT NULL,
    author character varying(255) DEFAULT NULL::character varying,
    cover_path character varying(255) DEFAULT NULL::character varying,
    file_path character varying(255),
    read_date timestamp(0) without time zone NOT NULL,
    description character varying(2048) DEFAULT NULL::character varying,
    mark integer
);


ALTER TABLE public.book OWNER TO bookshelf;

--
-- Name: book_id_seq; Type: SEQUENCE; Schema: public; Owner: bookshelf
--

CREATE SEQUENCE public.book_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.book_id_seq OWNER TO bookshelf;

--
-- Name: migration_versions; Type: TABLE; Schema: public; Owner: bookshelf
--

CREATE TABLE public.migration_versions (
    version character varying(14) NOT NULL,
    executed_at timestamp(0) without time zone NOT NULL
);


ALTER TABLE public.migration_versions OWNER TO bookshelf;

--
-- Name: COLUMN migration_versions.executed_at; Type: COMMENT; Schema: public; Owner: bookshelf
--

COMMENT ON COLUMN public.migration_versions.executed_at IS '(DC2Type:datetime_immutable)';


--
-- Name: user; Type: TABLE; Schema: public; Owner: bookshelf
--

CREATE TABLE public."user" (
    id integer NOT NULL,
    email character varying(180) NOT NULL,
    nickname character varying(32) NOT NULL,
    roles json NOT NULL,
    password character varying(255) NOT NULL,
    is_verified boolean NOT NULL
);


ALTER TABLE public."user" OWNER TO bookshelf;

--
-- Name: user_id_seq; Type: SEQUENCE; Schema: public; Owner: bookshelf
--

CREATE SEQUENCE public.user_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.user_id_seq OWNER TO bookshelf;

--
-- Data for Name: book; Type: TABLE DATA; Schema: public; Owner: bookshelf
--

COPY public.book (id, owner, book_name, author, cover_path, file_path, read_date, description, mark) FROM stdin;
\.


--
-- Data for Name: migration_versions; Type: TABLE DATA; Schema: public; Owner: bookshelf
--

COPY public.migration_versions (version, executed_at) FROM stdin;
20200609164949	2020-06-09 16:50:24
20200611145226	2020-06-11 14:52:44
20200612175059	2020-06-12 17:51:04
\.


--
-- Data for Name: user; Type: TABLE DATA; Schema: public; Owner: bookshelf
--

COPY public."user" (id, email, nickname, roles, password, is_verified) FROM stdin;
\.


--
-- Name: book_id_seq; Type: SEQUENCE SET; Schema: public; Owner: bookshelf
--

SELECT pg_catalog.setval('public.book_id_seq', 12, true);


--
-- Name: user_id_seq; Type: SEQUENCE SET; Schema: public; Owner: bookshelf
--

SELECT pg_catalog.setval('public.user_id_seq', 15, true);


--
-- Name: book book_pkey; Type: CONSTRAINT; Schema: public; Owner: bookshelf
--

ALTER TABLE ONLY public.book
    ADD CONSTRAINT book_pkey PRIMARY KEY (id);


--
-- Name: migration_versions migration_versions_pkey; Type: CONSTRAINT; Schema: public; Owner: bookshelf
--

ALTER TABLE ONLY public.migration_versions
    ADD CONSTRAINT migration_versions_pkey PRIMARY KEY (version);


--
-- Name: user user_pkey; Type: CONSTRAINT; Schema: public; Owner: bookshelf
--

ALTER TABLE ONLY public."user"
    ADD CONSTRAINT user_pkey PRIMARY KEY (id);


--
-- Name: idx_cbe5a331cf60e67c; Type: INDEX; Schema: public; Owner: bookshelf
--

CREATE INDEX idx_cbe5a331cf60e67c ON public.book USING btree (owner);


--
-- Name: uniq_8d93d649a188fe64; Type: INDEX; Schema: public; Owner: bookshelf
--

CREATE UNIQUE INDEX uniq_8d93d649a188fe64 ON public."user" USING btree (nickname);


--
-- Name: uniq_8d93d649e7927c74; Type: INDEX; Schema: public; Owner: bookshelf
--

CREATE UNIQUE INDEX uniq_8d93d649e7927c74 ON public."user" USING btree (email);


--
-- Name: book fk_cbe5a331cf60e67c; Type: FK CONSTRAINT; Schema: public; Owner: bookshelf
--

ALTER TABLE ONLY public.book
    ADD CONSTRAINT fk_cbe5a331cf60e67c FOREIGN KEY (owner) REFERENCES public."user"(id);


--
-- PostgreSQL database dump complete
--

