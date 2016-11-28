# Mibew Bot Framework Plugin

Plugin Bot framework to mibew.

## Installation

1. Get the archive with the plugin sources. At the moment the only option is to build the plugin from sources.

2. Untar/unzip the plugin's archive.

3. Put files of the plugins to the `<Mibew root>/plugins`  folder.

4. Add plugins configs to "plugins" structure in "`<Mibew root>`/configs/config.yml". If the "plugins" stucture looks like `plugins: []` it will become:
    ```yaml
    plugins:
        "TaisaPlus:Bot": # Plugin's configurations are described below
            BotName: "Option bot name cannot be empty"
            hostDirectLine: "Option hostDirectLine ...api/conversation cannot be empty"
            botSecret: "Option botSecret cannot be empty"
            hostToken: "Option hostToken ...api/token cannot be empty"
            message_error_user_bot_does_not_understand: "Optional message"
            message_error_user_bot_can_not_find_an_answer: "Optional message"
            message_error_user_bot_does_not_find_information: "Optional message"
            message_error_user_bot_does_not_have_access_to_information: "Optional message"
            ...
    ```
	Ejemplo
	```yaml
    plugins:
        "TaisaPlus:Bot": # Plugin's configurations are described below
            BotName: "EchoBotElias"
            hostDirectLine: "https://directline.botframework.com/api/conversations"
            botSecret: "GBNvay8yhg4.cwA.13A.wrEqcQ3xdh_x7axL3QmObNTXGrny8eCgxnrOqdCj0OM"
            hostToken: "https://directline.botframework.com/api/tokens"
            message_error_user_bot_does_not_understand: "No entiendo lo que dices..."
            message_error_user_bot_can_not_find_an_answer: "Espera un momento..."
            message_error_user_bot_does_not_find_information: "Espera un momento..."
            message_error_user_bot_does_not_have_access_to_information: "No encuentro información..."
            ...
    ```

5. Navigate to "`<Mibew Base URL>`/operator/plugin" page and enable the plugin.

## Build from sources

There are several actions one should do before use the latest version of the plugin from the repository:

1. Obtain a copy of the repository using `git clone`, download button, or another way.
2. Install [node.js](http://nodejs.org/) and [npm](https://www.npmjs.org/).
3. Install [Gulp](http://gulpjs.com/).
4. Install npm dependencies using `npm install`.
5. Run Gulp to build the sources using `gulp default`.

Finally `.tar.gz` and `.zip` archives of the ready-to-use Plugin will be available in `release` directory.

## Creating bots compatible with BotFramework
The bot must be able to return the following errors with messages:

1. message: 'Bot does not understand'
2. message: 'Bot can not find an answer'
3. message: 'Bot does not find information'
4. message: 'Bot does not have access to information'

