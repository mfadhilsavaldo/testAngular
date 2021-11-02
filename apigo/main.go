package main

import (
	"gorm.io/gorm"

	"github.com/gin-gonic/gin"
	"gorm.io/driver/mysql"
)

func KoneksiGo() *gorm.DB {
	dsn := "root:@tcp(127.0.0.1:3306)/testGo?charset=utf8mb4&parseTime=True&loc=Local"
	db, err := gorm.Open(mysql.Open(dsn), &gorm.Config{})

	if err != nil {
		return nil
	}

	return db
}

func main() {
	r := gin.Default()
	r.GET("/read", func(c *gin.Context) {
		db_go := KoneksiGo()
		var callback = gin.H{}
		status := 200

		result := []map[string]interface{}{}
		db_go.Raw("SELECT * FROM user").Scan(&result)

		callback["success"] = true
		callback["data"] = result

		c.JSON(status, callback)
	})
	r.POST("/create", func(context *gin.Context) {
		db_go := KoneksiGo()
		var callback = gin.H{}
		nama := context.PostForm("nama")
		email := context.PostForm("email")
		umur := context.PostForm("umur")

		//CEK PARAMETER POST
		callback["nama"] = nama
		callback["email"] = email
		callback["umur"] = umur

		data := map[string]interface{}{
			"nama":  nama,
			"email": email,
			"umur":  umur,
		}
		create := db_go.Table("user").Create(&data)

		if create.Error == nil {
			callback["success"] = true
			callback["data"] = data
		} else {
			callback["success"] = false
			callback["err"] = create.Error
		}

		context.JSON(200, callback)
	})

	r.POST("/edit", func(context *gin.Context) {
		db_go := KoneksiGo()
		var callback = gin.H{}
		iduser := context.PostForm("iduser")
		nama := context.PostForm("nama")
		email := context.PostForm("email")
		umur := context.PostForm("umur")

		result := db_go.Exec("UPDATE user SET nama=?, email=?, umur=? WHERE iduser=?", nama, email, umur, iduser)
		if result.Error == nil {
			callback["success"] = true
			callback["msg"] = "Data berhasil diupdate"
		} else {
			callback["success"] = false
			callback["msg"] = "Update Gagall"
		}

		//CEK PARAMETER POST
		callback["iduser"] = iduser
		callback["nama"] = nama
		callback["email"] = email
		callback["umur"] = umur

		context.JSON(200, callback)
	})

	r.POST("/delete", func(context *gin.Context) {
		db_go := KoneksiGo()
		var callback = gin.H{}
		iduser := context.PostForm("iduser")

		result := db_go.Exec("DELETE FROM user WHERE iduser=?", iduser)
		if result.Error == nil {
			callback["success"] = true
			callback["msg"] = "Data berhasil dihapus"
		} else {
			callback["success"] = false
			callback["msg"] = "Hapus Gagal"
		}

		//CEK PARAMETER POST
		callback["iduser"] = iduser

		context.JSON(200, callback)
	})

	r.Run() // listen and serve on 0.0.0.0:8080 (for windows "localhost:8080")
}
