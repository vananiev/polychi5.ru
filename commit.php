git status | grep modified |perl -ane '$_=~/modified:\s+(.*)$/; print $1,"\n";' | xargs -i git add {}
git status | grep deleted |perl -ane '$_=~/deleted:\s+(.*)$/; print $1,"\n";' | xargs -i git rm {}
