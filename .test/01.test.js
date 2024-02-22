const { BaseTest } = require("./BaseTest.js")
const { By, until, Key, Select } = require("selenium-webdriver");
const assert = require('assert');
 
// heredem una classe amb un sol mètode test()
// emprem this.driver per utilitzar Selenium
 
class MyTest extends BaseTest
{
    async test() {
        await this.driver.get("http://localhost:8080");
        var register_button = await this.driver.findElement(By.css("a[href='register.php']"));
        await this.driver.actions()
        .move({ origin: register_button })
        .click()
        .perform()

        // Comprova que existeixen els inputs de register i inserta les dades corresponents
        let input_username = await this.driver.wait(until.elementLocated(By.id("register_name"), 20))
        assert(input_username, "ERROR TEST: input 'username' no trobat")
        await input_username.sendKeys("Selenium Tester");
        await input_username.sendKeys(Key.ENTER);

        let input_email = await this.driver.wait(until.elementLocated(By.id("register_email")), 20)
        assert(input_email, "ERROR TEST: input 'email' no trobat")
        await input_email.sendKeys("selenium2@test.com")
        await input_email.sendKeys(Key.ENTER)

        let input_password = await this.driver.wait(until.elementLocated(By.id("register_pass"), 20))
        assert(input_password, "ERROR TEST: input 'password' no trobat")
        await input_password.sendKeys("aaaaAaa1");
        await input_password.sendKeys(Key.TAB);

        let input_password_confirm = await this.driver.wait(until.elementLocated(By.id("register_repeat_pass"), 20))
        assert(input_password_confirm, "ERROR TEST: input 'confirm password' no trobat")
        await input_password_confirm.sendKeys("aaaaAaa1");
        await input_password.sendKeys(Key.ENTER);

        let input_country = new Select(await this.driver.wait(until.elementLocated(By.id("register_pais"))), 20)
        assert(input_country, "ERROR TEST: select 'country' no trobat")
        await input_country.selectByVisibleText("España")

        let input_phone = await this.driver.wait(until.elementLocated(By.id("register_tel")), 20)
        assert(input_phone, "ERROR TEST: input 'phone number' no trobat")
        await input_phone.sendKeys("123456989")
        await input_phone.sendKeys(Key.ENTER)

        let input_city = await this.driver.wait(until.elementLocated(By.id("register_ciudad")), 20)
        assert(input_city, "ERROR TEST: input 'city' no trobat")
        await input_city.sendKeys("Cornella de Llobregat")
        await input_city.sendKeys(Key.ENTER)

        // Fa el submit i busca el popup de registre completat al recarregar la pagina
        let submit = await this.driver.wait(until.elementLocated(By.id("submit")), 20)
        assert(submit, "ERROR TEST: botó submit no trobat")
        await this.driver.actions()
        .move({ origin: submit})
        .click()
        .perform()
        let notificationSuccess = false
        try {
            let success_dialog = await this.driver.wait(until.elementLocated(By.id("mailVerification")), 2000)
            if (success_dialog) {
                notificationSuccess = true
            }
        } catch (error) {}
        assert(notificationSuccess, "ERROR TEST: register no completat")
        console.log("TEST OK");
    }
}

(async () => {
	const test = new MyTest();
	await test.run();
	console.log("END")
})();