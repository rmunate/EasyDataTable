---
title: Introduction
editLink: true
outline: deep
---

![EasyDataTable](/img/logo-full-banner.png)

## Introduction

**Welcome to EasyDataTable!**

Are you tired of the hassle of handling DataTables in your Laravel projects? Say goodbye to the complexity! EasyDataTable is here to streamline your backend processes.

With EasyDataTable, you can harness the power of Laravel's Query Builder to swiftly and effortlessly create tables with all the features you need, as demanded by [DataTables](https://datatables.net/). 

### Explore our Frontend Compatibility!

![Frontend Technologies](/img/fronts.png)

Discover the seamless integration with various frontends. Whether you're using jQuery, Vue.js, Angular, or React, EasyDataTable has got you covered!

## Table Types
Below, we detail the difference between the two types of tables that you can implement in your project, depending on the amount of data you are working with.

### ClientSide
The main feature of a ClientSide DataTable is that all data and data manipulation logic are handled on the client side, i.e., in the user's web browser, rather than making requests to the web server for each interaction. This means that all the data needed to populate the table is initially loaded in the user's browser, and operations such as search, filtering, sorting, and pagination are performed without the need to communicate with the server.

### ServerSide
The main feature of a ServerSide DataTable is that most data-related operations are performed on the server side rather than the client side. Here are some key aspects of a ServerSide DataTable:

**Efficient Data Loading:** In a ServerSide DataTable, data is loaded from the server as the user navigates the table or performs actions such as search, filtering, sorting, and pagination. This allows for handling large data sets without overloading the user's browser.

**Server Requests:** When an action that requires data manipulation, such as sorting the table, is performed, the DataTable sends a request to the web server. The server processes the request and returns the corresponding data.

**Efficient Filtering and Searching:** Filtering and searching are performed on the server side, which means that only relevant data is sent to the client. This is especially useful when working with large amounts of data.

**Security:** By performing most operations on the server, you can implement greater security and authorization control to ensure that users only access data they have permission to access.