# ageage

TODO write application description

## Requirements

* [composer](https://getcomposer.org/)
* [Docker](https://docs.docker.com/)

## Installation for developer

Edit environment variables

```bash
cp .env.example .env
```

edit .env
`DB_HOST=mysql`
`DB_USER=karaage`

```bash
vendor/bin/sail up -d
vendor/bin/sail artisan key:generate
vendor/bin/sail artisan migrate
vendor/bin/sail shell -c 'pip install -r app/python/requirements.txt'
vendor/bin/sail npm install
vendor/bin/sail npm run dev

```

## Deployment

[Amazon Elastic Beanstalk](https://www.notion.so/Teamspace-Home-57942e4b8f544eb1a367263dc2937018?p=dc2916022c8846ceabbd82c38d3d269b&pm=c)
