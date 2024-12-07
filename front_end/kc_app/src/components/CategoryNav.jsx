import React, { useState, useEffect } from "react";
import { Container, Row, Col, ListGroup, Spinner } from "react-bootstrap";
import CourseCard from "./CourseCard";
import axios from "axios";
import { backend } from "../config/config";

const CategoryNav = () => {
  const [categories, setCategories] = useState([]);
  const [expandedCategories, setExpandedCategories] = useState({});
  const [loadingCategories, setLoadingCategories] = useState(false);
  const [selectedCategory, setSelectedCategory] = useState();
  const [courses, setCourses] = useState([]);
  const [loadingCourses, setLoadingCourses] = useState(false);

  useEffect(() => {
    fetchCategories();
  }, []);

  useEffect(() => {
    fetchCourses(selectedCategory);
  }, [selectedCategory]);

  const fetchCategories = async () => {
    setLoadingCategories(true);
    try {
      const response = await axios.get(
        `${backend}/api/V1/categories/get_categories.php`
      );
      setCategories(response.data);
    } catch (error) {
      console.error("Error fetching categories:", error);
    } finally {
      setLoadingCategories(false);
    }
  };

  const fetchSubcategories = async (categoryId) => {
    try {
      const response = await axios.get(
        `${backend}/api/V1/categories/get_categories.php?parentId=${categoryId}`
      );
      const subcategories = response.data;
      setCategories((prevCategories) =>
        prevCategories.map((cat) =>
          cat.id === categoryId ? { ...cat, subcategories } : cat
        )
      );
    } catch (error) {
      console.error("Error fetching subcategories:", error);
    }
  };

  const fetchCourses = async (categoryId = null) => {
    setLoadingCourses(true);
    try {
      const response = await axios.get(
        categoryId
          ? `${backend}/api/V1/courses/get_courses.php?categoryId=${categoryId}`
          : `${backend}/api/V1/courses/get_courses.php`
      );
      setCourses(response.data);
    } catch (error) {
      console.error("Error fetching courses:", error);
    } finally {
      setLoadingCourses(false);
    }
  };

  const handleCategoryClick = (category) => {
    setSelectedCategory(category.id);
    if (category.id in expandedCategories) {
      setExpandedCategories((prev) => {
        const { [category.id]: _, ...rest } = prev;
        return rest;
      });
    } else {
      setExpandedCategories((prev) => ({ ...prev, [category.id]: true }));
      if (!category.subcategories) {
        fetchSubcategories(category.id);
      }
    }
  };

  const handleSelectCategory = (categoryId) => {
    setSelectedCategory(categoryId);
    fetchCourses(categoryId);
  };

  return (
    <Container>
      <Row>
        <Col md={3} sm={12} className="mt-4">
          <h5>Categories</h5>
          {loadingCategories ? (
            <Spinner animation="border" />
          ) : (
            <ListGroup>
              <ListGroup.Item
                onClick={() => handleSelectCategory()}
                active={selectedCategory === "All"}
              >
                All Courses
              </ListGroup.Item>
              {categories?.map((category) => (
                <div key={category.id}>
                  <ListGroup.Item
                    onClick={() => handleCategoryClick(category)}
                    style={{ cursor: "pointer" }}
                  >
                    {category.name} ({category.count})
                  </ListGroup.Item>
                  {expandedCategories[category.id] &&
                    category.subcategories && (
                      <ListGroup className="ms-3">
                        {category.subcategories?.map((subcat) => (
                          <ListGroup.Item
                            key={subcat.id}
                            onClick={() => handleSelectCategory(subcat.id)}
                            active={selectedCategory === subcat.id}
                            style={{ cursor: "pointer" }}
                          >
                            {subcat.name} ({subcat.count})
                          </ListGroup.Item>
                        ))}
                      </ListGroup>
                    )}
                </div>
              ))}
            </ListGroup>
          )}
        </Col>

        <Col md={9} sm={12}>
          <h3 className="mt-4">Course Catalog</h3>
          {loadingCourses ? (
            <Spinner animation="border" />
          ) : (
            <Row>
              {courses.map((course) => (
                <Col md={4} sm={12} xs={12} key={course.id} className="mb-4">
                  <CourseCard
                    title={course.title}
                    description={course.description}
                    category={course.category}
                  />
                </Col>
              ))}
            </Row>
          )}
        </Col>
      </Row>
    </Container>
  );
};

export default CategoryNav;
