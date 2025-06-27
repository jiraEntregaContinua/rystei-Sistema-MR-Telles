*** Settings ***
Library    SeleniumLibrary

*** Variables ***
${URL}          http://localhost:8000/register
${NOME}         Test User
${EMAIL}        testuser@example.com
${SENHA}        123456
${CHROMEDRIVER}   D:\\facul\\estagio\\chromedriver-win64\\chromedriver.exe

*** Test Cases ***
Teste Cadastro Usuário
    Create WebDriver    Chrome
    Go To    ${URL}
    Input Text    id=name     ${NOME}
    Input Text    id=email    ${EMAIL}
    Input Text    id=password ${SENHA}
    Input Text    id=password_confirmation  ${SENHA}
    Click Button  xpath=//button[contains(text(),'Cadastrar')]
    Page Should Contain    Cadastro
    Close Browser

