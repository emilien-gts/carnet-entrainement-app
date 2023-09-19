## Name

🧌 Carnet d'entrainement

## Context

It's a personal project. Its aim was to follow my evolution in bodybuilding.

## Description

This project contains :
- Symfony project configuration
- Docker configuration
- Makefile
- Many tools for development

### Symfony project configuration

This project is based on my [Symfony 6.3 skeleton](https://github.com/emilien-gts/symfony-skeleton).

## Installation

With the Makefile, you just need to make a :

    make dc-install

To do this, you need to install [make](https://doc.ubuntu-fr.org/ubuntu-make).

## How to use ?

Once installed, the interface is visually similar to an administration system. You'll be able to manage exercises, sessions and track your sessions.

The command

    make sf c="app:exercise:sync"

will import all basic exercises, based on the exercises.json in the data folder.

## Project Status

The project is interrupted. In fact, I wasn't really enjoying coding the project any more. 