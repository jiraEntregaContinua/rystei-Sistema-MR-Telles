*** Settings ***
Library           SeleniumLibrary
Resource          ../resources/login_keywords.robot
Variables         ../variables/common_variables.robot

*** Test Cases ***
Login com sucesso
    [Tags]    positivo
    Acessar página de login
    Preencher email e senha
    Clicar no botão entrar
    Validar redirecionamento para dashboard