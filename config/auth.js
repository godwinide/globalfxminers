module.exports = {
    ensureAuthenticated: function(req, res, next) {
      if (req.isAuthenticated()) {
        return next();
      }
      req.flash('error_msg', 'Login required');
      res.redirect('/trade/signin');
    },
    ensureIsAdmin: function (req, res, next) {
      if (!(req.isAuthenticated && req.user?.isAdmin == true)) {
          return next()
      }
      res.redirect("/admin/signin");
    },
    forwardAuthenticated: function(req, res, next) {
      if (req.isAuthenticated()) {
        return next();
      }
      res.redirect('/');      
    }
  };