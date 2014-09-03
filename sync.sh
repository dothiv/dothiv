#!/bin/bash

app/console --env=prod contentful:sync -S xfj3l4zlkg3j -t 872d3b646d8690f2697641031e7d6afa87d5b0670b07f46cf3eb4fa68988ee3d
app/console --env=prod contentful:sync -S q9io9aidddk4 -t 6d158a644c9c09a5eb57ca2fa516fc2148e32cad34523ad2696b82ee909aba54
app/console --env=prod contentful:sync -S d6rba6hzdb1o -t ca1953f1714a77d52b1782c6002898b7d5fcc42a50cf198e231193c8408f858d
app/console --env=prod contentful:assets:cache
app/console --env=prod dothiv:contentful:images
