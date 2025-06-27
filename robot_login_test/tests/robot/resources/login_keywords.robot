*** Keywords ***
Acessar página de login
    Open Browser    ${LOGIN_URL}    ${BROWSER}
    Maximize Browser Window

Preencher email e senha
    Input Text    id=email    ${EMAIL}
    Input Text    id=password    ${PASSWORD}

Clicar no botão entrar
    Click Button    xpath=//button[contains(text(), 'ENTRAR')]

Validar redirecionamento para dashboard
    Wait Until Page Contains    Bem-vindo