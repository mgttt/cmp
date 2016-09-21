rem git pull --progress --no-rebase -v "origin" master:1
rem git pull --progress --no-rebase -v "sae" 1
del /s /f /q *.swp
del /s /f /q *.un~
git pull --progress --no-rebase
git status
git add .
git status
git commit -m "auto upload %time%"
rem git push --progress "sae" master:1
rem git push --progress "origin" master:1
git push --progress
pause