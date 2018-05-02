git checkout master
git status
git commit -am "Committing changes to master"
git push origin master
git checkout gh-pages
git rebase master # or merge, whatever your preference
git push origin gh-pages
git checkout master
