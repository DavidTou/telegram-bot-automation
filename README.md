# Telegram BOT for Home Automation

Using Telegram's BOT api platform (https://core.telegram.org/bots/api), I created a system that could allowed me to remotely control and manage my house. The project was tested on a scaled model. The goal was to develop a free domotic system that could use a preexisting communication platform (Telegram messaging app).A server was used as centralized node between house's sensors/switches and Telegram's servers. All the data was sent through HTTPS Protocol to insure security.
This system allows easy scalability and maintenance because the application is entirely server-side. Used languages: PHP, Arduino language, and SQL.

![telegram-bot-automation](https://github.com/DavidTou/telegram-bot-automation/blob/master/info/home-schema.png "Home Automation Schema")

![telegram-bot-automation](https://github.com/DavidTou/telegram-bot-automation/blob/master/info/why-telegram.png "Why Telegram?")

## Implementation details

- server/html/config.php - config file
- server/html/func.php - functions for Telegram Bot API communication
- server/html/saveimage.php - get image from WebCam and save to file
- server/html/script.php - Main code that is web hooked by Telegram Bot API
- server/html/start_msg.php - Notify user that bot is active
- server/html/stop_msg.php - Server turning off message
- server/html/TCPDF - TCPDF files to generate PDFs


## Getting Started

IMPORTANT !! Read https://core.telegram.org/bots/api

Add server/html files to your https Server directory.
Edit the config.php file with Telegram® BOT API Token, BOT ID, Owners ID Tokens and various IoT device Addresses.
Make sure your support SSL/TLS. Telegram only allow you communication through Https.

## Authors

* **David Tougaw** - *Initial work* - [DavidTou](https://github.com/DavidTou)

See also the list of [contributors](https://github.com/your/project/contributors) who participated in this project.

## License

This project is licensed under the MIT License - see the [LICENSE.md](LICENSE.md) file for details
