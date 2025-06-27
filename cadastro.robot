*** Settings ***
Library    SeleniumLibrary

*** Variables ***
${URL}    https://example.com

*** Test Cases ***
Teste Simples
    Open Browser    ${URL}    chrome
    Page Should Contain    Example Domain
    Close Browser
