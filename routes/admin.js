const router = require("express").Router();
const {ensureIsAdmin} = require("../config/auth");
const User = require("../model/User");
const History = require("../model/History");
const bcrypt = require("bcryptjs");
const uuid = require("uuid");
const path = require("path");
const comma = require("../utils/comma");

router.get("/dashboard", ensureIsAdmin, (req,res) => {
    try{
        return res.render("admin/dashboard", {pageTitle: "Dashbaord", req, comma, layout: "layout2"});
    }catch(err){
        return res.redirect("/");
    }
});


module.exports = router;