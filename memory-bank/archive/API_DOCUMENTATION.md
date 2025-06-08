# Context7 Job Portal API Documentation

## Overview
RESTful API for Vue3 SPA frontend following Laravel 12 best practices.

## Authentication
All API endpoints require Bearer token authentication except login/register.

### Headers
```
Authorization: Bearer {token}
Content-Type: application/json
Accept: application/json
```

## Endpoints

### Authentication
- `POST /api/v1/auth/login` - Login user
- `POST /api/v1/auth/register` - Register user  
- `POST /api/v1/auth/logout` - Logout user
- `GET /api/v1/auth/user` - Get authenticated user

### Resources

#### Home
- `GET /api/v1/home` - List all home
- `POST /api/v1/home` - Create new Home
- `GET /api/v1/home/{id}` - Get specific Home
- `PUT /api/v1/home/{id}` - Update Home
- `DELETE /api/v1/home/{id}` - Delete Home

#### Admin
- `GET /api/v1/admin` - List all admin
- `POST /api/v1/admin` - Create new Admin
- `GET /api/v1/admin/{id}` - Get specific Admin
- `PUT /api/v1/admin/{id}` - Update Admin
- `DELETE /api/v1/admin/{id}` - Delete Admin

#### BrandingSlider
- `GET /api/v1/brandingslider` - List all brandingslider
- `POST /api/v1/brandingslider` - Create new BrandingSlider
- `GET /api/v1/brandingslider/{id}` - Get specific BrandingSlider
- `PUT /api/v1/brandingslider/{id}` - Update BrandingSlider
- `DELETE /api/v1/brandingslider/{id}` - Delete BrandingSlider

#### Candidate
- `GET /api/v1/candidate` - List all candidate
- `POST /api/v1/candidate` - Create new Candidate
- `GET /api/v1/candidate/{id}` - Get specific Candidate
- `PUT /api/v1/candidate/{id}` - Update Candidate
- `DELETE /api/v1/candidate/{id}` - Delete Candidate

#### MasterData
- `GET /api/v1/masterdata` - List all masterdata
- `POST /api/v1/masterdata` - Create new MasterData
- `GET /api/v1/masterdata/{id}` - Get specific MasterData
- `PUT /api/v1/masterdata/{id}` - Update MasterData
- `DELETE /api/v1/masterdata/{id}` - Delete MasterData

#### Cms
- `GET /api/v1/cms` - List all cms
- `POST /api/v1/cms` - Create new Cms
- `GET /api/v1/cms/{id}` - Get specific Cms
- `PUT /api/v1/cms/{id}` - Update Cms
- `DELETE /api/v1/cms/{id}` - Delete Cms

#### CompanySize
- `GET /api/v1/companysize` - List all companysize
- `POST /api/v1/companysize` - Create new CompanySize
- `GET /api/v1/companysize/{id}` - Get specific CompanySize
- `PUT /api/v1/companysize/{id}` - Update CompanySize
- `DELETE /api/v1/companysize/{id}` - Delete CompanySize

#### EmailTemplate
- `GET /api/v1/emailtemplate` - List all emailtemplate
- `POST /api/v1/emailtemplate` - Create new EmailTemplate
- `GET /api/v1/emailtemplate/{id}` - Get specific EmailTemplate
- `PUT /api/v1/emailtemplate/{id}` - Update EmailTemplate
- `DELETE /api/v1/emailtemplate/{id}` - Delete EmailTemplate

#### FunctionalArea
- `GET /api/v1/functionalarea` - List all functionalarea
- `POST /api/v1/functionalarea` - Create new FunctionalArea
- `GET /api/v1/functionalarea/{id}` - Get specific FunctionalArea
- `PUT /api/v1/functionalarea/{id}` - Update FunctionalArea
- `DELETE /api/v1/functionalarea/{id}` - Delete FunctionalArea

#### HeaderSlider
- `GET /api/v1/headerslider` - List all headerslider
- `POST /api/v1/headerslider` - Create new HeaderSlider
- `GET /api/v1/headerslider/{id}` - Get specific HeaderSlider
- `PUT /api/v1/headerslider/{id}` - Update HeaderSlider
- `DELETE /api/v1/headerslider/{id}` - Delete HeaderSlider

#### ImageSlider
- `GET /api/v1/imageslider` - List all imageslider
- `POST /api/v1/imageslider` - Create new ImageSlider
- `GET /api/v1/imageslider/{id}` - Get specific ImageSlider
- `PUT /api/v1/imageslider/{id}` - Update ImageSlider
- `DELETE /api/v1/imageslider/{id}` - Delete ImageSlider

#### Job
- `GET /api/v1/job` - List all job
- `POST /api/v1/job` - Create new Job
- `GET /api/v1/job/{id}` - Get specific Job
- `PUT /api/v1/job/{id}` - Update Job
- `DELETE /api/v1/job/{id}` - Delete Job

#### ReportedJob
- `GET /api/v1/reportedjob` - List all reportedjob
- `POST /api/v1/reportedjob` - Create new ReportedJob
- `GET /api/v1/reportedjob/{id}` - Get specific ReportedJob
- `PUT /api/v1/reportedjob/{id}` - Update ReportedJob
- `DELETE /api/v1/reportedjob/{id}` - Delete ReportedJob

#### SalaryCurrency
- `GET /api/v1/salarycurrency` - List all salarycurrency
- `POST /api/v1/salarycurrency` - Create new SalaryCurrency
- `GET /api/v1/salarycurrency/{id}` - Get specific SalaryCurrency
- `PUT /api/v1/salarycurrency/{id}` - Update SalaryCurrency
- `DELETE /api/v1/salarycurrency/{id}` - Delete SalaryCurrency

#### SalaryPeriod
- `GET /api/v1/salaryperiod` - List all salaryperiod
- `POST /api/v1/salaryperiod` - Create new SalaryPeriod
- `GET /api/v1/salaryperiod/{id}` - Get specific SalaryPeriod
- `PUT /api/v1/salaryperiod/{id}` - Update SalaryPeriod
- `DELETE /api/v1/salaryperiod/{id}` - Delete SalaryPeriod

#### Subscriber
- `GET /api/v1/subscriber` - List all subscriber
- `POST /api/v1/subscriber` - Create new Subscriber
- `GET /api/v1/subscriber/{id}` - Get specific Subscriber
- `PUT /api/v1/subscriber/{id}` - Update Subscriber
- `DELETE /api/v1/subscriber/{id}` - Delete Subscriber

#### Transaction
- `GET /api/v1/transaction` - List all transaction
- `POST /api/v1/transaction` - Create new Transaction
- `GET /api/v1/transaction/{id}` - Get specific Transaction
- `PUT /api/v1/transaction/{id}` - Update Transaction
- `DELETE /api/v1/transaction/{id}` - Delete Transaction

#### BlogComment
- `GET /api/v1/blogcomment` - List all blogcomment
- `POST /api/v1/blogcomment` - Create new BlogComment
- `GET /api/v1/blogcomment/{id}` - Get specific BlogComment
- `PUT /api/v1/blogcomment/{id}` - Update BlogComment
- `DELETE /api/v1/blogcomment/{id}` - Delete BlogComment

#### Application
- `GET /api/v1/application` - List all application
- `POST /api/v1/application` - Create new Application
- `GET /api/v1/application/{id}` - Get specific Application
- `PUT /api/v1/application/{id}` - Update Application
- `DELETE /api/v1/application/{id}` - Delete Application

#### Location
- `GET /api/v1/location` - List all location
- `POST /api/v1/location` - Create new Location
- `GET /api/v1/location/{id}` - Get specific Location
- `PUT /api/v1/location/{id}` - Update Location
- `DELETE /api/v1/location/{id}` - Delete Location

#### Company
- `GET /api/v1/company` - List all company
- `POST /api/v1/company` - Create new Company
- `GET /api/v1/company/{id}` - Get specific Company
- `PUT /api/v1/company/{id}` - Update Company
- `DELETE /api/v1/company/{id}` - Delete Company

#### Dashboard
- `GET /api/v1/dashboard` - List all dashboard
- `POST /api/v1/dashboard` - Create new Dashboard
- `GET /api/v1/dashboard/{id}` - Get specific Dashboard
- `PUT /api/v1/dashboard/{id}` - Update Dashboard
- `DELETE /api/v1/dashboard/{id}` - Delete Dashboard

#### Swagger
- `GET /api/v1/swagger` - List all swagger
- `POST /api/v1/swagger` - Create new Swagger
- `GET /api/v1/swagger/{id}` - Get specific Swagger
- `PUT /api/v1/swagger/{id}` - Update Swagger
- `DELETE /api/v1/swagger/{id}` - Delete Swagger

#### SwaggerAsset
- `GET /api/v1/swaggerasset` - List all swaggerasset
- `POST /api/v1/swaggerasset` - Create new SwaggerAsset
- `GET /api/v1/swaggerasset/{id}` - Get specific SwaggerAsset
- `PUT /api/v1/swaggerasset/{id}` - Update SwaggerAsset
- `DELETE /api/v1/swaggerasset/{id}` - Delete SwaggerAsset

#### FrontendAssets
- `GET /api/v1/frontendassets` - List all frontendassets
- `POST /api/v1/frontendassets` - Create new FrontendAssets
- `GET /api/v1/frontendassets/{id}` - Get specific FrontendAssets
- `PUT /api/v1/frontendassets/{id}` - Update FrontendAssets
- `DELETE /api/v1/frontendassets/{id}` - Delete FrontendAssets

#### FilePreview
- `GET /api/v1/filepreview` - List all filepreview
- `POST /api/v1/filepreview` - Create new FilePreview
- `GET /api/v1/filepreview/{id}` - Get specific FilePreview
- `PUT /api/v1/filepreview/{id}` - Update FilePreview
- `DELETE /api/v1/filepreview/{id}` - Delete FilePreview

#### HandleRequests
- `GET /api/v1/handlerequests` - List all handlerequests
- `POST /api/v1/handlerequests` - Create new HandleRequests
- `GET /api/v1/handlerequests/{id}` - Get specific HandleRequests
- `PUT /api/v1/handlerequests/{id}` - Update HandleRequests
- `DELETE /api/v1/handlerequests/{id}` - Delete HandleRequests

#### FileUpload
- `GET /api/v1/fileupload` - List all fileupload
- `POST /api/v1/fileupload` - Create new FileUpload
- `GET /api/v1/fileupload/{id}` - Get specific FileUpload
- `PUT /api/v1/fileupload/{id}` - Update FileUpload
- `DELETE /api/v1/fileupload/{id}` - Delete FileUpload

#### RealTime
- `GET /api/v1/realtime` - List all realtime
- `POST /api/v1/realtime` - Create new RealTime
- `GET /api/v1/realtime/{id}` - Get specific RealTime
- `PUT /api/v1/realtime/{id}` - Update RealTime
- `DELETE /api/v1/realtime/{id}` - Delete RealTime

#### CsrfCookie
- `GET /api/v1/csrfcookie` - List all csrfcookie
- `POST /api/v1/csrfcookie` - Create new CsrfCookie
- `GET /api/v1/csrfcookie/{id}` - Get specific CsrfCookie
- `PUT /api/v1/csrfcookie/{id}` - Update CsrfCookie
- `DELETE /api/v1/csrfcookie/{id}` - Delete CsrfCookie

#### Sitemap
- `GET /api/v1/sitemap` - List all sitemap
- `POST /api/v1/sitemap` - Create new Sitemap
- `GET /api/v1/sitemap/{id}` - Get specific Sitemap
- `PUT /api/v1/sitemap/{id}` - Update Sitemap
- `DELETE /api/v1/sitemap/{id}` - Delete Sitemap

#### Payment
- `GET /api/v1/payment` - List all payment
- `POST /api/v1/payment` - Create new Payment
- `GET /api/v1/payment/{id}` - Get specific Payment
- `PUT /api/v1/payment/{id}` - Update Payment
- `DELETE /api/v1/payment/{id}` - Delete Payment

#### Webhook
- `GET /api/v1/webhook` - List all webhook
- `POST /api/v1/webhook` - Create new Webhook
- `GET /api/v1/webhook/{id}` - Get specific Webhook
- `PUT /api/v1/webhook/{id}` - Update Webhook
- `DELETE /api/v1/webhook/{id}` - Delete Webhook

#### WireUiAssets
- `GET /api/v1/wireuiassets` - List all wireuiassets
- `POST /api/v1/wireuiassets` - Create new WireUiAssets
- `GET /api/v1/wireuiassets/{id}` - Get specific WireUiAssets
- `PUT /api/v1/wireuiassets/{id}` - Update WireUiAssets
- `DELETE /api/v1/wireuiassets/{id}` - Delete WireUiAssets

#### Unknown
- `GET /api/v1/unknown` - List all unknown
- `POST /api/v1/unknown` - Create new Unknown
- `GET /api/v1/unknown/{id}` - Get specific Unknown
- `PUT /api/v1/unknown/{id}` - Update Unknown
- `DELETE /api/v1/unknown/{id}` - Delete Unknown

## Response Format
```json
{
    "success": true,
    "message": "Operation successful",
    "data": {},
    "pagination": {
        "current_page": 1,
        "last_page": 10,
        "per_page": 15,
        "total": 150
    }
}
```

## Error Handling
```json
{
    "success": false,
    "message": "Error description",
    "error": "Detailed error message"
}
```
