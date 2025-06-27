*** Settings ***
Library    SeleniumLibrary

*** Variables ***
${URL}          http://localhost:8000/register
${NOME}         Test User
${EMAIL}        testuser@example.com
${SENHA}        123456

*** Test Cases ***
Teste Cadastro Usuário
    ${options}=    Evaluate    sys.modules['selenium.webdriver'].ChromeOptions()    sys, selenium.webdriver
    Create WebDriver    Chrome    options=${options}   # Sem service e sem caminho do chromedriver
    Go To    ${URL}
    Input Text    id=name    ${NOME}
    Input Text    id=email    ${EMAIL}
    Input Text    id=password    ${SENHA}
    Input Text    id=password_confirmation    ${SENHA}
    Click Button    xpath=//button[contains(text(),'Cadastrar')]
    Page Should Contain    Cadastro
    Close Browser
